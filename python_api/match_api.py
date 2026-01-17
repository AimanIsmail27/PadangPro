# match_api.py (Railway-Ready Version — minimal deployment changes)

from flask import Flask, request, jsonify
import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import mysql.connector
from datetime import datetime
import json
import os  # ✅ added for Railway env vars + config

# ---------------------------------------------------
# 1. FUZZY LOGIC SETUP (UNCHANGED)
# ---------------------------------------------------

skill_diff = ctrl.Antecedent(np.arange(0, 5, 1), 'skill_diff')
skill_diff['perfect'] = fuzz.trimf(skill_diff.universe, [0, 0, 1])
skill_diff['good'] = fuzz.trimf(skill_diff.universe, [0, 1, 3])
skill_diff['poor'] = fuzz.gaussmf(skill_diff.universe, 4, 2)

availability = ctrl.Antecedent(np.arange(0, 101, 1), 'availability')
availability['poor'] = fuzz.trimf(availability.universe, [0, 0, 50])
availability['good'] = fuzz.trimf(availability.universe, [25, 100, 100])

intensity_match = ctrl.Antecedent(np.arange(0, 101, 1), 'intensity_match')
intensity_match['no'] = fuzz.trimf(intensity_match.universe, [0, 0, 1])
intensity_match['yes'] = fuzz.trimf(intensity_match.universe, [100, 100, 100])

compatibility = ctrl.Consequent(np.arange(0, 101, 1), 'compatibility')
compatibility['low'] = fuzz.trimf(compatibility.universe, [0, 0, 40])
compatibility['medium'] = fuzz.trimf(compatibility.universe, [20, 50, 80])
compatibility['high'] = fuzz.trimf(compatibility.universe, [60, 100, 100])

rule1 = ctrl.Rule(skill_diff['perfect'] & availability['good'] & intensity_match['yes'], compatibility['high'])
rule2 = ctrl.Rule(skill_diff['good'] & availability['good'], compatibility['medium'])
rule3 = ctrl.Rule(skill_diff['poor'] | availability['poor'], compatibility['low'])

compatibility_ctrl = ctrl.ControlSystem([rule1, rule2, rule3])
matchmaking_system = ctrl.ControlSystemSimulation(compatibility_ctrl)

print("✓ Fuzzy logic controller ready (DEBUG MODE).")

# ---------------------------------------------------
# 2. HELPER FUNCTIONS (UNCHANGED)
# ---------------------------------------------------

def parse_availability(raw):
    try:
        return json.loads(raw) if raw else {"days": [], "time": []}
    except:
        return {"days": [], "time": []}

def calculate_availability_score(player, ad_datetime):
    availability_raw = player.get('customer_Availability')
    availability = parse_availability(availability_raw)

    ad_day = ad_datetime.strftime('%A')
    ad_hour = ad_datetime.hour

    player_days = availability.get('days', [])
    player_times = availability.get('time', [])

    day_ok = ad_day in player_days

    time_ok = False
    for t in player_times:
        if t == 'Morning' and 6 <= ad_hour < 12:
            time_ok = True
        elif t == 'Afternoon' and 12 <= ad_hour < 18:
            time_ok = True
        elif t == 'Night' and (ad_hour >= 18 or ad_hour < 6):
            time_ok = True

    if day_ok and time_ok:
        score = 95
    elif day_ok:
        score = 60
    else:
        score = 10

    print("  Availability:")
    print(f"    Ad day={ad_day} hour={ad_hour}")
    print(f"    Player days={player_days}")
    print(f"    Player times={player_times}")
    print(f"    Day match={day_ok} Time match={time_ok}")
    print(f"    Availability score={score}")

    return score

def calculate_intensity_score(player_intensity, ad_intensity):
    if not player_intensity or not ad_intensity:
        return 0
    return 100 if player_intensity == ad_intensity else 0

def get_compatibility_score(player, ad):
    try:
        player_skill = player.get('customer_SkillLevel')
        ad_skill = ad.get('ads_TargetSkillLevel')

        if player_skill is None or ad_skill is None:
            skill_diff_val = 4
        else:
            skill_diff_val = abs(int(player_skill) - int(ad_skill))

        ad_datetime = ad.get('ads_SlotTime')
        availability_score = calculate_availability_score(player, ad_datetime)

        player_intensity = player.get('customer_Intensity', 'Fun')
        ad_intensity = ad.get('ads_MatchIntensity')
        intensity_score = calculate_intensity_score(player_intensity, ad_intensity)

        print(f"  Skill: player={player_skill} ad={ad_skill} diff={skill_diff_val}")
        print(f"  Intensity: player={player_intensity} ad={ad_intensity} match={'YES' if intensity_score == 100 else 'NO'}")

        matchmaking_system.input['skill_diff'] = skill_diff_val
        matchmaking_system.input['availability'] = availability_score
        matchmaking_system.input['intensity_match'] = intensity_score

        matchmaking_system.compute()
        score = matchmaking_system.output['compatibility']

        print(f"  => Final compatibility score: {score:.2f}\n")
        return score

    except Exception as e:
        print(f"❌ Error computing score: {e}")
        return 0

# ---------------------------------------------------
# 3. FLASK API (MINIMAL DEPLOYMENT CHANGES ONLY)
# ---------------------------------------------------

app = Flask(__name__)

@app.route("/health/db", methods=["GET"])
def health_db():
    try:
        conn = mysql.connector.connect(**db_config)
        cur = conn.cursor()
        cur.execute("SELECT 1")
        cur.fetchone()
        cur.close()
        conn.close()
        return jsonify({"status": "ok", "db": "connected"}), 200
    except Exception as e:
        return jsonify({"status": "error", "db_error": str(e)}), 500



# ✅ Railway-ready DB config (uses Railway MySQL env vars if present; falls back to localhost for local dev)
db_config = {
    "host": os.getenv("MYSQLHOST", "127.0.0.1"),
    "port": int(os.getenv("MYSQLPORT", "3306")),
    "user": os.getenv("MYSQLUSER", "root"),
    "password": os.getenv("MYSQLPASSWORD", ""),
    "database": os.getenv("MYSQLDATABASE", "footballreservationdb"),
}

@app.route('/match', methods=['POST'])
def find_matches():
    player = request.json
    customer_id = player.get('customerID')

    if not customer_id:
        return jsonify({'error': 'customerID required'}), 400

    print(f"\n[{datetime.now().strftime('%H:%M:%S')}] Match request for customer {customer_id}")

    # ✅ Added basic try/except so Railway returns JSON instead of crashing (deployment-safe)
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        cursor.execute("""
            SELECT * FROM advertisement
            WHERE customerID != %s
            AND ads_Status = 'Active'
            AND ads_SlotTime > NOW()
        """, (customer_id,))

        ads = cursor.fetchall()
        cursor.close()
        conn.close()

    except Exception as e:
        print(f"❌ Database connection/query error: {e}")
        return jsonify({'error': 'Database error', 'details': str(e)}), 500

    results = []

    for ad in ads:
        print(f"Scoring Ad {ad['adsID']}")
        score = get_compatibility_score(player, ad)
        results.append({
            'adsID': ad['adsID'],
            'compatibility_score': score
        })

    results.sort(key=lambda x: x['compatibility_score'], reverse=True)
    return jsonify(results)

# ---------------------------------------------------
# 4. RUN SERVER (LOCAL ONLY)
# NOTE: On Railway you should run via gunicorn:
# gunicorn match_api:app --bind 0.0.0.0:$PORT
# ---------------------------------------------------

if __name__ == '__main__':
    print("=" * 60)
    print("🤖 PadangPro Matchmaking API – VERBOSE DEBUG MODE")
    print("=" * 60)
    app.run(host='0.0.0.0', port=5001, debug=True)
# End of match_api.py