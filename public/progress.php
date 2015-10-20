<?php 
  set_time_limit(0); // Don't timeout.
  header( 'Content-type: text/html; charset=utf-8' ); 
?><!doctype html>
<html>
<body>
<progress id="progress" max="10" value="1">
</progress>
<div id="test">
Hello world
</div>

<?php
// Apparently this is needed to disable buffering.
ob_implicit_flush(true);
ob_end_flush();

$colors = array('red', 'green', 'blue', 'yellow', 'red', 'green', 'black', 'white', 'blue', 'green', 'pink');
for ($i = 1; $i <= 10; ++$i)
{
  // Outputting a large chunk of data is also mentioned as a work-around, but for me it didn't work.
  // echo str_repeat(' ', 10000);

  // Instead of sleeping, this is where your actual processing would go.
  sleep(1);

  // Echo a small Javascript to update the status.
  ?>
    <script>
    document.getElementById('progress').value = <?=$i?>;
    document.getElementById('test').style.backgroundColor = '<?=$colors[$i]?>';
    document.getElementById('test').innerText = '<?=$i*10?>% done';
    </script>
  <?php
  // The script is flushed right away, and the browser should execute it right away.
  flush();
}