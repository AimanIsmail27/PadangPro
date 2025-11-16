# match_api.py (Corrected Version - Filter Removed)

from flask import Flask, request, jsonify
import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import mysql.connector
from datetime import datetime

# --- 1. Define the Fuzzy Logic "Brain" (Unchanged) ---
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
print("✓ Fuzzy logic controller built successfully.")

# --- 2. Helper Functions (Now Robust) ---

def calculate_availability_score(player, ad_datetime):
    # This is still a placeholder. You will need to implement real logic here.
    # For now, it returns a high score to allow other logic to be tested.
    return 80 

def calculate_intensity_score(player_intensity, ad_intensity):
    # If either value is missing, they can't match.
    if not player_intensity or not ad_intensity:
        return 0
    return 100 if player_intensity == ad_intensity else 0

def get_compatibility_score(player, ad):
    """
    Calculates a compatibility score, now safely handling None/NULL values.
    """
    try:
        # Get values, defaulting to a "neutral" or "non-match" value if None
        player_skill = player.get('customer_SkillLevel')
        ad_target_skill = ad.get('ads_TargetSkillLevel')
        
        # If either skill level is missing, we can't calculate a skill difference.
        if player_skill is None or ad_target_skill is None:
            skill_diff_val = 4 # Assign a "poor" difference if data is missing
        else:
            skill_diff_val = abs(int(player_skill) - int(ad_target_skill))
        
        ad_datetime = ad.get('ads_SlotTime')
        availability_score = calculate_availability_score(player, ad_datetime)
        
        player_intensity = player.get('customer_Intensity', 'Fun') # Default to 'Fun'
        ad_intensity = ad.get('ads_MatchIntensity')
        intensity_score = calculate_intensity_score(player_intensity, ad_intensity)

        # Feed values into the fuzzy system
        matchmaking_system.input['skill_diff'] = skill_diff_val
        matchmaking_system.input['availability'] = availability_score
        matchmaking_system.input['intensity_match'] = intensity_score
        
        matchmaking_system.compute()
        
        return matchmaking_system.output['compatibility']
    except Exception as e:
        print(f"Error computing score: {e}")
        return 0

# --- 3. Flask API Setup (Unchanged) ---
app = Flask(__name__)
db_config = { 'user': 'root', 'password': '', 'host': '127.0.0.1', 'database': 'footballreservationdb' }

@app.route('/match', methods=['POST'])
def find_matches():
    player_profile = request.json
    player_customer_id = player_profile.get('customerID')

    if not player_customer_id:
        return jsonify({'error': 'customerID is required'}), 400

    print(f"\n[{datetime.now().strftime('%H:%M:%S')}] Received match request for customer: {player_customer_id}")

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        query = "SELECT * FROM advertisement WHERE customerID != %s AND ads_Status = 'Active' AND ads_SlotTime > NOW()"
        cursor.execute(query, (player_customer_id,))
        all_ads = cursor.fetchall()

        if not all_ads:
            print("No active ads found in database.")
            cursor.close()
            conn.close()
            return jsonify([]) # Return an empty list

        scored_ads = []
        for ad in all_ads:
            score = get_compatibility_score(player_profile, ad)
            print(f"  - Scoring Ad {ad['adsID']}... Score: {score:.2f}")
            
            # --- THIS IS THE FIX ---
            # The "if score > 30" filter is removed.
            # We now append every ad with its score.
            scored_ads.append({
                'adsID': ad['adsID'],
                'compatibility_score': score
            })
        
        sorted_ads = sorted(scored_ads, key=lambda x: x['compatibility_score'], reverse=True)
        cursor.close()
        conn.close()

        print(f"Returning {len(sorted_ads)} compatible ads, sorted by score.")
        return jsonify(sorted_ads)

    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
        return jsonify({'error': 'Database connection failed'}), 500
    except Exception as e:
        print(f"General Error: {e}")
        return jsonify({'error': 'An unknown error occurred'}), 500

# --- 4. Run the Server (Unchanged) ---
if __name__ == '__main__':
    print("=" * 60)
    print("🤖 Starting PadangPro Fuzzy Matchmaking API (v2 - Robust)")
    print("=" * 60)
    app.run(host='0.0.0.0', port=5001, debug=True)