@extends('layout')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-tooltip"></script>

    <div class="container mt-4">
        <div class="border p-4">
            <h1 class="h5 mb-4">
                {{ $user_id }}
            </h1>
            <div style="width: 75%; height: 400px; display: block; margin: auto;">
                <canvas id="rankChart"></canvas>
            </div>

            <script>
                var ctx = document.getElementById('rankChart').getContext('2d');
                var contests = @json($contest_titles);
                var rankChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($contest_dates),
                        datasets: [{
                            label: '順位',
                            data: @json($contest_ranks),
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: false,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                reverse: true,
                                beginAtZero: false,
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                type: 'time',
                                time: {
                                    parser: 'Y.M.D H:m',
                                    unit: 'month',
                                    tooltipFormat: 'Y.M.D',
                                    displayFormats: {
                                        month: 'YYYY/MM'
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';

                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y;
                                        }
                                        return label;
                                    },
                                    title: function(tooltipItems) {
                                        var date = tooltipItems[0].label;
                                        var contest = contests[tooltipItems[0].dataIndex];
                                        return moment(date).format('Y.M.D') + ' - ' + contest;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
            @if (!empty($contest_ids))
                <h2 class="h5">
                    過去の成績表
                </h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>コンテスト名</th>
                            <th>順位</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($contest_ids); $i++)
                            <tr>
                                <td><a class="text-decoration-none"
                                    href="{{ route('contests.show', ['contest' => $contest_ids[$i]]) }}">
                                    {{ $contest_titles[$i] }}
                                </a></td>
                                <td>{{ $contest_ranks[$i] }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
