<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.6;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #92400e;
        }

        .meta {
            font-size: 11px;
            color: #4b5563;
            margin-top: 6px;
        }

        .section {
            margin-top: 24px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 10px;
            border-left: 4px solid #f59e0b;
            padding-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #fef3c7;
        }

        table th,
        table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
        }

        table th {
            text-align: left;
            font-weight: bold;
            color: #92400e;
        }

        table td.value {
            text-align: right;
            font-weight: bold;
        }

        .summary-grid {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background-color: #f9fafb;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .summary-label {
            color: #4b5563;
        }

        .summary-value {
            font-weight: bold;
            color: #111827;
        }

        .insight-box {
            background-color: #ecfeff;
            border: 1px solid #67e8f9;
            padding: 12px;
            border-radius: 6px;
        }

        .insight-box ul {
            padding-left: 16px;
            margin: 0;
        }

        .insight-box li {
            margin-bottom: 6px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="meta">
            Report Type: {{ ucwords(str_replace('_', ' ', $reportType)) }} <br>
            Generated at: {{ $generatedAt->format('d M Y, h:i A') }}
        </div>
    </div>

    {{-- SUMMARY --}}
    @if(!empty($summaryData))
    <div class="section">
        <div class="section-title">Report Summary</div>

        <div class="summary-grid">
            @foreach($summaryData as $label => $value)
                <div class="summary-row">
                    <span class="summary-label">{{ ucwords(str_replace('_',' ', $label)) }}</span>
                    <span class="summary-value">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- TABLE DATA --}}
    <div class="section">
        <div class="section-title">Detailed Results</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 70%;">Period / Category</th>
                    <th style="width: 30%; text-align:right;">Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labels as $index => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="value">
                            {{ $values[$index] ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align:center; color:#6b7280;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- INSIGHTS --}}
    @if(!empty($insights))
    <div class="section">
        <div class="section-title">Insights & Interpretation</div>

        <div class="insight-box">
            <ul>
                @foreach($insights as $insight)
                    <li>{{ $insight }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        PadangPro Reporting System • Generated Automatically
    </div>

</body>
</html>
