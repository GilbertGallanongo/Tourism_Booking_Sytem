<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 6px; margin: 8px; color: #333;">
    <h1 style="font-size: 13px; margin-bottom: 4px; color: #1a1a2e;">{{ $title }}</h1>
    <div style="font-size: 8px; color: #666; margin-bottom: 4px;">Time Period: {{ $periodLabel }}</div>
    <div style="font-size: 8px; color: #666; margin-bottom: 10px;">Generated: {{ $generatedAt }}</div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 6px; table-layout: fixed;">
        <thead>
            <tr>
                @foreach($headers as $index => $header)
                    <th style="background-color: #1a1a2e; color: white; padding: 3px 2px; text-align: left; font-weight: bold; border: 1px solid #333; width: {{ 100 / count($headers) }}%; line-height: 1.1;">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td style="padding: 3px 2px; border: 1px solid #ddd; text-align: left; word-wrap: break-word; line-height: 1.1; vertical-align: top;">{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 12px; font-size: 8px; color: #666; border-top: 1px solid #ddd; padding-top: 8px;">
        Total Records: {{ count($rows) }}
    </div>
</body>
</html>
