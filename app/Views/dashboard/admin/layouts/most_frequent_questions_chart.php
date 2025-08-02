<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagram Frekuensi Berdasarkan Tag</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        .chart-container {
            height: 469px;
            position: relative;
            top: 4rem;
            width: 599px;
            right: -47px;
        }
        .container-body-chart {
            position: relative;
            background-color: white;
            height: 609px;
            top: -598px;
            width: 42%;
            left: 56%;
            border-radius: 30px 30px 30px 30px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
    </style>
</head>
<body>
<div class="container-body-chart">
    <div class="chart-container">
        <canvas id="tagsChart"></canvas>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tags = [];
        const frequencies = [];
        const backgroundColors = [];
        
        // Array warna pastel yang telah ditentukan
        const pastelColors = [
            'rgba(187, 222, 251, 0.7)',  // Baby Blue
            'rgba(209, 196, 233, 0.7)',  // Lavender
            'rgba(255, 224, 178, 0.7)',  // Peach
            'rgba(200, 230, 201, 0.7)',  // Mint Green
            'rgba(255, 204, 188, 0.7)',  // Salmon
            'rgba(225, 190, 231, 0.7)',  // Light Purple
            'rgba(178, 235, 242, 0.7)',  // Light Cyan
            'rgba(255, 183, 195, 0.7)',  // Pink
            'rgba(255, 241, 118, 0.7)',  // Light Yellow
            'rgba(197, 225, 165, 0.7)',  // Light Green
            'rgba(244, 143, 177, 0.7)',  // Rose
            'rgba(129, 212, 250, 0.7)'   // Sky Blue
        ];
        
        // Border colors (slightly darker versions of background colors)
        const borderColors = [
            'rgba(144, 202, 249, 1)',    // Darker Baby Blue
            'rgba(179, 157, 219, 1)',    // Darker Lavender
            'rgba(255, 204, 128, 1)',    // Darker Peach
            'rgba(165, 214, 167, 1)',    // Darker Mint Green
            'rgba(255, 171, 145, 1)',    // Darker Salmon
            'rgba(206, 147, 216, 1)',    // Darker Light Purple
            'rgba(128, 222, 234, 1)',    // Darker Light Cyan
            'rgba(255, 153, 170, 1)',    // Darker Pink
            'rgba(255, 235, 59, 1)',     // Darker Light Yellow
            'rgba(174, 213, 129, 1)',    // Darker Light Green
            'rgba(240, 98, 146, 1)',     // Darker Rose
            'rgba(79, 195, 247, 1)'      // Darker Sky Blue
        ];

        <?php if (isset($tagFrequencies) && is_array($tagFrequencies)): ?>
            <?php $index = 0; ?>
            <?php foreach ($tagFrequencies as $tag => $data): ?>
                tags.push("<?= str_replace('"', '\"', $tag) ?>");
                frequencies.push(<?= $data['total_frequency'] ?>);
                backgroundColors.push(pastelColors[<?= $index ?> % pastelColors.length]);
                <?php $index++; ?>
            <?php endforeach; ?>
        <?php else: ?>
            // Data contoh jika tidak ada data dari PHP
            const sampleTags = ['Akun', 'Teknis', 'Produk', 'Preferensi', 'Harga'];
            const sampleFrequencies = [95, 227, 86, 29, 27];
            
            sampleTags.forEach((tag, index) => {
                tags.push(tag);
                frequencies.push(sampleFrequencies[index]);
                backgroundColors.push(pastelColors[index % pastelColors.length]);
            });
        <?php endif; ?>

        const ctx = document.getElementById('tagsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: tags,
                datasets: [{
                    label: 'Frekuensi Berdasarkan Tag',
                    data: frequencies,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Frekuensi'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tag'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Frekuensi Pertanyaan Berdasarkan Tag',
                        font: {
                            size: 18
                        }
                    },
                    tooltip: {
                        callbacks: {
                            footer: function(tooltipItems) {
                                const index = tooltipItems[0].dataIndex;
                                <?php if (isset($tagFrequencies) && is_array($tagFrequencies)): ?>
                                    const questionCounts = [
                                        <?php foreach ($tagFrequencies as $tag => $data): ?>
                                            <?= $data['question_count'] ?>,
                                        <?php endforeach; ?>
                                    ];
                                    return `Jumlah Pertanyaan: ${questionCounts[index]}`;
                                <?php else: ?>
                                    // Data contoh jumlah pertanyaan
                                    const sampleQuestionCounts = [3, 4, 2, 1, 1];
                                    return `Jumlah Pertanyaan: ${sampleQuestionCounts[index]}`;
                                <?php endif; ?>
                            }
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>