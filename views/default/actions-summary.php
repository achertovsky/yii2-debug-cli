<section class="container">
    <div id="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <th>Route</th>
                    <th>Total Count</th>
                    <th>Total Time (ms)</th>
                    <th>Total Memory (MB)</th>
                    <th>Avg. Time (ms)</th>
                    <th>Avg. Memory (MB)</th>
                    <?php
                        foreach ($results as $url => $result) {
                            echo "<tr>";
                            echo "<td title='$url'>".substr($url, 0, 50)."</td>";
                            echo "<td>{$result['totalCount']}</td>";
                            echo "<td>".Yii::$app->formatter->asDecimal($result['totalTime'], 0)."</td>";
                            echo "<td>".Yii::$app->formatter->asDecimal($result['totalMemory'], 0)."</td>";
                            echo "<td>".Yii::$app->formatter->asDecimal($result['totalTime']/$result['totalCount'], 0)."</td>";
                            echo "<td>".Yii::$app->formatter->asDecimal($result['totalMemory']/$result['totalCount'], 0)."</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>
</section>
