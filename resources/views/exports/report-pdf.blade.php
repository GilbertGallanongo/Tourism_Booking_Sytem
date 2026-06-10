<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 10px; margin: 20px; color: #333;">
    <h1 style="font-size: 14px; margin-bottom: 5px; color: #1a1a2e;">{{ $title }}</h1>
    <div style="font-size: 9px; color: #666; margin-bottom: 5px;">Time Period: {{ $periodLabel }}</div>
    <div style="font-size: 9px; color: #666; margin-bottom: 15px;">Generated: {{ $generatedAt }}</div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; table-layout: fixed;">
        <thead>
            <tr>
                @foreach($headers as $index => $header)
                    <th style="background-color: #1a1a2e; color: white; padding: 5px 8px; text-align: left; font-weight: bold; border: 1px solid #333; width: {{ 100 / count($headers) }}%;">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td style="padding: 4px 8px; border: 1px solid #ddd; text-align: left; word-wrap: break-word;">{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
        Total Records: {{ count($rows) }}
    </div>
</body>
</html>
