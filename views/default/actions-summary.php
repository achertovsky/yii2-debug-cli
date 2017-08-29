<section class="container">
    <div id="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <th>Route</th>
                    <th>Total Time</th>
                    <th>Total Memory</th>
                    <th>Total Count</th>
                    <th>Avg. Time</th>
                    <th>Avg. Memory</th>
                    <?php
                        foreach ($results as $url => $result) {
                            echo "<tr>";
                            echo "<td title='$url'>".substr($url, 0, 50)."</td>";
                            echo "<td>{$result['totalTime']}</td>";
                            echo "<td>{$result['totalMemory']}</td>";
                            echo "<td>{$result['totalCount']}</td>";
                            echo "<td>".$result['totalTime']/$result['totalCount']."</td>";
                            echo "<td>".$result['totalMemory']/$result['totalCount']."</td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>
</section>
