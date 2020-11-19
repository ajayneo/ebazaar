<?php
class Neo_Innoviti_Helper_Data extends Mage_Core_Helper_Abstract
{
	function emiCalc($annual_interest_Rate, $tenurecode, $txn_Amt, $processingFee = 0)
	{
		$tenure = $tenurecode * 3;
		$part1=$txn_Amt * $annual_interest_Rate/(12*100);
		$part2=pow(1+$annual_interest_Rate/(12*100),$tenure);
		$part3=(pow(1+$annual_interest_Rate/(12*100),$tenure)) - 1;
		$emi=($part1 * $part2) / $part3;
		$interest_amount = $emi*$tenure-$txn_Amt;
		$total_Amount_Payable = $emi*$tenure;
		
		$ary['interest_amount'] = (int)ceil($interest_amount);
		$ary['including_inr'] = (int)ceil($total_Amount_Payable);
		$ary['interest'] = number_format($annual_interest_Rate,2,".","");
		$ary['duration'] = $tenure.' Months';
		$ary['processingFee'] = number_format($processingFee,2,".","");
		$ary['installment'] = (int)ceil($total_Amount_Payable / $tenure);
		return $ary;
	}
}
?>
