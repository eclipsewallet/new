<?php

/*
 * Created by 2C2P
 * Date 19 June 2017
 * P2c2pCurrencyCode helper class is give the 2C2P payment gateway suppored currency code.
 */

namespace P2c2p\P2c2pPayment\Helper;
use Magento\Framework\App\Helper\AbstractHelper;

class P2c2pCurrencyCode extends AbstractHelper
{
	// This function is used to get the currency code with currency number and exponent.
	public function getP2c2pSupportedCurrenyCode(){
		return array( 
			"CLF" => array ("Num" => "990","Exponent" => "4",),
			"BHD" => array ("Num" => "048","Exponent" => "3",),
			"IQD" => array ("Num" => "368","Exponent" => "3",),
			"JOD" => array ("Num" => "400","Exponent" => "3",),
			"KWD" => array ("Num" => "414","Exponent" => "3",),
			"LYD" => array ("Num" => "434","Exponent" => "3",),
			"OMR" => array ("Num" => "512","Exponent" => "3",),
			"TND" => array ("Num" => "788","Exponent" => "3",),
			"AED" => array ("Num" => "784","Exponent" => "2",),
			"AFN" => array ("Num" => "971","Exponent" => "2",),
			"ALL" => array ("Num" => "008","Exponent" => "2",),
			"AMD" => array ("Num" => "051","Exponent" => "2",),
			"ANG" => array ("Num" => "532","Exponent" => "2",),
			"AOA" => array ("Num" => "973","Exponent" => "2",),
			"ARS" => array ("Num" => "032","Exponent" => "2",),
			"AUD" => array ("Num" => "036","Exponent" => "2",),
			"AWG" => array ("Num" => "533","Exponent" => "2",),
			"AZN" => array ("Num" => "944","Exponent" => "2",),
			"BAM" => array ("Num" => "977","Exponent" => "2",),
			"BBD" => array ("Num" => "052","Exponent" => "2",),
			"BDT" => array ("Num" => "050","Exponent" => "2",),
			"BGN" => array ("Num" => "975","Exponent" => "2",),
			"BMD" => array ("Num" => "060","Exponent" => "2",),
			"BND" => array ("Num" => "096","Exponent" => "2",),
			"BOB" => array ("Num" => "068","Exponent" => "2",),
			"BOV" => array ("Num" => "984","Exponent" => "2",),
			"BRL" => array ("Num" => "986","Exponent" => "2",),
			"BSD" => array ("Num" => "044","Exponent" => "2",),
			"BTN" => array ("Num" => "064","Exponent" => "2",),
			"BWP" => array ("Num" => "072","Exponent" => "2",),
			"BYN" => array ("Num" => "933","Exponent" => "2",),
			"BZD" => array ("Num" => "084","Exponent" => "2",),
			"CAD" => array ("Num" => "124","Exponent" => "2",),
			"CDF" => array ("Num" => "976","Exponent" => "2",),
			"CHE" => array ("Num" => "947","Exponent" => "2",),
			"CHF" => array ("Num" => "756","Exponent" => "2",),
			"CHW" => array ("Num" => "948","Exponent" => "2",),
			"CNY" => array ("Num" => "156","Exponent" => "2",),
			"COP" => array ("Num" => "170","Exponent" => "2",),
			"COU" => array ("Num" => "970","Exponent" => "2",),
			"CRC" => array ("Num" => "188","Exponent" => "2",),
			"CUC" => array ("Num" => "931","Exponent" => "2",),
			"CUP" => array ("Num" => "192","Exponent" => "2",),
			"CZK" => array ("Num" => "203","Exponent" => "2",),
			"DKK" => array ("Num" => "208","Exponent" => "2",),
			"DOP" => array ("Num" => "214","Exponent" => "2",),
			"DZD" => array ("Num" => "012","Exponent" => "2",),
			"EGP" => array ("Num" => "818","Exponent" => "2",),
			"ERN" => array ("Num" => "232","Exponent" => "2",),
			"ETB" => array ("Num" => "230","Exponent" => "2",),
			"EUR" => array ("Num" => "978","Exponent" => "2",),
			"FJD" => array ("Num" => "242","Exponent" => "2",),
			"FKP" => array ("Num" => "238","Exponent" => "2",),
			"GBP" => array ("Num" => "826","Exponent" => "2",),
			"GEL" => array ("Num" => "981","Exponent" => "2",),
			"GHS" => array ("Num" => "936","Exponent" => "2",),
			"GIP" => array ("Num" => "292","Exponent" => "2",),
			"GMD" => array ("Num" => "270","Exponent" => "2",),
			"GTQ" => array ("Num" => "320","Exponent" => "2",),
			"GYD" => array ("Num" => "328","Exponent" => "2",),
			"HKD" => array ("Num" => "344","Exponent" => "2",),
			"HNL" => array ("Num" => "340","Exponent" => "2",),
			"HRK" => array ("Num" => "191","Exponent" => "2",),
			"HTG" => array ("Num" => "332","Exponent" => "2",),
			"HUF" => array ("Num" => "348","Exponent" => "2",),
			"IDR" => array ("Num" => "360","Exponent" => "2",),
			"ILS" => array ("Num" => "376","Exponent" => "2",),
			"INR" => array ("Num" => "356","Exponent" => "2",),
			"IRR" => array ("Num" => "364","Exponent" => "2",),
			"JMD" => array ("Num" => "388","Exponent" => "2",),
			"KES" => array ("Num" => "404","Exponent" => "2",),
			"KGS" => array ("Num" => "417","Exponent" => "2",),
			"KHR" => array ("Num" => "116","Exponent" => "2",),
			"KPW" => array ("Num" => "408","Exponent" => "2",),
			"KYD" => array ("Num" => "136","Exponent" => "2",),
			"KZT" => array ("Num" => "398","Exponent" => "2",),
			"LAK" => array ("Num" => "418","Exponent" => "2",),
			"LBP" => array ("Num" => "422","Exponent" => "2",),
			"LKR" => array ("Num" => "144","Exponent" => "2",),
			"LRD" => array ("Num" => "430","Exponent" => "2",),
			"LSL" => array ("Num" => "426","Exponent" => "2",),
			"MAD" => array ("Num" => "504","Exponent" => "2",),
			"MDL" => array ("Num" => "498","Exponent" => "2",),
			"MKD" => array ("Num" => "807","Exponent" => "2",),
			"MMK" => array ("Num" => "104","Exponent" => "2",),
			"MNT" => array ("Num" => "496","Exponent" => "2",),
			"MOP" => array ("Num" => "446","Exponent" => "2",),
			"MUR" => array ("Num" => "480","Exponent" => "2",),
			"MVR" => array ("Num" => "462","Exponent" => "2",),
			"MWK" => array ("Num" => "454","Exponent" => "2",),
			"MXN" => array ("Num" => "484","Exponent" => "2",),
			"MXV" => array ("Num" => "979","Exponent" => "2",),
			"MYR" => array ("Num" => "458","Exponent" => "2",),
			"MZN" => array ("Num" => "943","Exponent" => "2",),
			"NAD" => array ("Num" => "516","Exponent" => "2",),
			"NGN" => array ("Num" => "566","Exponent" => "2",),
			"NIO" => array ("Num" => "558","Exponent" => "2",),
			"NOK" => array ("Num" => "578","Exponent" => "2",),
			"NPR" => array ("Num" => "524","Exponent" => "2",),
			"NZD" => array ("Num" => "554","Exponent" => "2",),
			"PAB" => array ("Num" => "590","Exponent" => "2",),
			"PEN" => array ("Num" => "604","Exponent" => "2",),
			"PGK" => array ("Num" => "598","Exponent" => "2",),
			"PHP" => array ("Num" => "608","Exponent" => "2",),
			"PKR" => array ("Num" => "586","Exponent" => "2",),
			"PLN" => array ("Num" => "985","Exponent" => "2",),
			"QAR" => array ("Num" => "634","Exponent" => "2",),
			"RON" => array ("Num" => "946","Exponent" => "2",),
			"RSD" => array ("Num" => "941","Exponent" => "2",),
			"RUB" => array ("Num" => "643","Exponent" => "2",),
			"SAR" => array ("Num" => "682","Exponent" => "2",),
			"SBD" => array ("Num" => "090","Exponent" => "2",),
			"SCR" => array ("Num" => "690","Exponent" => "2",),
			"SDG" => array ("Num" => "938","Exponent" => "2",),
			"SEK" => array ("Num" => "752","Exponent" => "2",),
			"SGD" => array ("Num" => "702","Exponent" => "2",),
			"SHP" => array ("Num" => "654","Exponent" => "2",),
			"SLL" => array ("Num" => "694","Exponent" => "2",),
			"SOS" => array ("Num" => "706","Exponent" => "2",),
			"SRD" => array ("Num" => "968","Exponent" => "2",),
			"SSP" => array ("Num" => "728","Exponent" => "2",),
			"STD" => array ("Num" => "678","Exponent" => "2",),
			"SVC" => array ("Num" => "222","Exponent" => "2",),
			"SYP" => array ("Num" => "760","Exponent" => "2",),
			"SZL" => array ("Num" => "748","Exponent" => "2",),
			"THB" => array ("Num" => "764","Exponent" => "2",),
			"TJS" => array ("Num" => "972","Exponent" => "2",),
			"TMT" => array ("Num" => "934","Exponent" => "2",),
			"TOP" => array ("Num" => "776","Exponent" => "2",),
			"TRY" => array ("Num" => "949","Exponent" => "2",),
			"TTD" => array ("Num" => "780","Exponent" => "2",),
			"TWD" => array ("Num" => "901","Exponent" => "2",),
			"TZS" => array ("Num" => "834","Exponent" => "2",),
			"UAH" => array ("Num" => "980","Exponent" => "2",),
			"USD" => array ("Num" => "840","Exponent" => "2",),
			"USN" => array ("Num" => "997","Exponent" => "2",),
			"UYU" => array ("Num" => "858","Exponent" => "2",),
			"UZS" => array ("Num" => "860","Exponent" => "2",),
			"VEF" => array ("Num" => "937","Exponent" => "2",),
			"WST" => array ("Num" => "882","Exponent" => "2",),
			"XCD" => array ("Num" => "951","Exponent" => "2",),
			"YER" => array ("Num" => "886","Exponent" => "2",),
			"ZAR" => array ("Num" => "710","Exponent" => "2",),
			"ZMW" => array ("Num" => "967","Exponent" => "2",),
			"ZWL" => array ("Num" => "932","Exponent" => "2",),
			"MGA" => array ("Num" => "969","Exponent" => "1",),
			"MRO" => array ("Num" => "478","Exponent" => "1",),
			"BIF" => array ("Num" => "108","Exponent" => "0",),
			"BYR" => array ("Num" => "974","Exponent" => "0",),
			"CLP" => array ("Num" => "152","Exponent" => "0",),
			"CVE" => array ("Num" => "132","Exponent" => "0",),
			"DJF" => array ("Num" => "262","Exponent" => "0",),
			"GNF" => array ("Num" => "324","Exponent" => "0",),
			"ISK" => array ("Num" => "352","Exponent" => "0",),
			"JPY" => array ("Num" => "392","Exponent" => "0",),
			"KMF" => array ("Num" => "174","Exponent" => "0",),
			"KRW" => array ("Num" => "410","Exponent" => "0",),
			"PYG" => array ("Num" => "600","Exponent" => "0",),
			"RWF" => array ("Num" => "646","Exponent" => "0",),
			"UGX" => array ("Num" => "800","Exponent" => "0",),
			"UYI" => array ("Num" => "940","Exponent" => "0",),
			"VND" => array ("Num" => "704","Exponent" => "0",),
			"VUV" => array ("Num" => "548","Exponent" => "0",),
			"XAF" => array ("Num" => "950","Exponent" => "0",),
			"XOF" => array ("Num" => "952","Exponent" => "0",),
			"XPF" => array ("Num" => "953","Exponent" => "0",),
		);
	}

	// This function is used to get the Exponents values and it is used to calculate with selected currency code.
	public function getP2c2pSupportedCurrencyExponents() {
		return array(
			"1" => "10",
			"2" => "100",
			"3" => "1000",
			"4" => "10000",
			"5" => "100000"
			); 
		}
}