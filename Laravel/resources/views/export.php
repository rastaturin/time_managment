<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
</head>
<body>
<ul>
<?php  foreach ($dates as $date => $data) { ?>
    <li>
        <ul>
            <li>Date: <?php echo $date ?></li>
            <li>Total time: <?php echo $data['time'] ?> h</li>
            <li>Notes:
                <ul>
                    <?php foreach ($data['notes'] as $note) {?>
                        <li><?php echo $note ?></li>
                    <?php } ?>
                </ul>
            </li>

        </ul>
    </li>
<?php  } ?>
</ul>
</body>
</html>