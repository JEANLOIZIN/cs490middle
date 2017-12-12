<?php

$storedResponse='test';
$ifStatement=-1;
$forLoop=-1;
$whileLoop=-1;
$submittedQuestionID=-1;
$totalCases=-1;
$testsPassed=-1;
$score=-1;
$points=-1;
$execResult='test';
$return_arr = array();

$crl = curl_init();
curl_setopt($crl, CURLOPT_URL, "https://web.njit.edu/~rh249/CS490/GetResponses.php");
curl_setopt($crl, CURLOPT_POST, 1);
curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
curl_setopt($crl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
$c=(curl_exec($crl));

$submit = json_decode($c, true);

foreach($submit as $row) {
	if ($submittedQuestionID!=$row['submittedQuestionID']){
		if ($submittedQuestionID!=-1){
			$score=$points*($testsPassed/$totalCases);
      
      if ($forLoop==1){
        if (strpos($storedResponse, 'for ') !== false) {
          $score=$score*0.80;
        }
      }
      
      if ($whileLoop==1){
        if (strpos($storedResponse, 'while ') !== true) {
          $score=$score*0.80;
        }
      }
      
      if ($ifStatement==1){
        if (strpos($storedResponse, 'if ') !== true) {
          $score=$score*0.80;
        }
      }
            
			$row_array['submittedQuestionID'] = $submittedQuestionID;
			$row_array['score'] = $score;
			array_push($return_arr,$row_array);
		}
		$submittedQuestionID=$row['submittedQuestionID'];
		$points=$row['points'];
		$testsPassed=0;
		$totalCases=0;
	}
  $forLoop = $row['forLoop'];
  $whileLoop = $row['whileLoop'];
  $ifStatement = $row['ifStatement'];
  $storedResponse = $row['questionResponse'];
  
  $myfile = fopen("/afs/cad.njit.edu/u/r/h/rh249/public_html/CS490/PyDir/pythonExec.py", "w") or die("Unable to open file.");
  fwrite($myfile, $row['questionResponse']);
  fwrite($myfile, "\n");
  fwrite($myfile, "print(");
  fwrite($myfile, $row['functionName']);
  fwrite($myfile, '(');
  fwrite($myfile, $row['testCase']);
  fwrite($myfile, '))');
  fclose($myfile);
  
	$execResult = exec('python /afs/cad.njit.edu/u/r/h/rh249/public_html/CS490/PyDir/pythonExec.py');  
  $totalCases = $totalCases + 1;

	if ($execResult==$execResult){
		$testsPassed = $testsPassed+1;
	}
	else{
	   $row_array['resonfordeduct'] = $row_array['resonfordeduct'] . " Expected ". $execResult . "But you provided ". $execResult + "," ;
	}
}
		if ($submittedQuestionID!=-1){
			$score=$points*($testsPassed/$totalCases);
      
      if ($forLoop==1){
        if (strpos($storedResponse, 'for ') !== true) {
          $score=$score*0.80;
        }
		else{
			   $row_array['resonfordeduct'] = $row_array['resonfordeduct'] . " Expected For loop , " ;
		}
      }
      
      if ($whileLoop==1){
        if (strpos($storedResponse, 'while ') !== true) {
          $score=$score*0.80;
        }
		else{
			   $row_array['resonfordeduct'] = $row_array['resonfordeduct'] . " Expected while loop , " ;
		}
      }
      
      if ($ifStatement==1){
        if (strpos($storedResponse, 'if ') !== true) {
          $score=$score*0.80;
        }
		else{
			   $row_array['resonfordeduct'] = $row_array['resonfordeduct'] . " Expected If Condition , " ;
		}
      }
            
			$row_array['submittedQuestionID'] = $submittedQuestionID;
			$row_array['score'] = $score;
			array_push($return_arr,$row_array);
		}
$gradesData = json_encode($return_arr);
echo $gradesData;
?>