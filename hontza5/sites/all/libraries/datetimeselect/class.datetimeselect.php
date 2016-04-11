<?php

/************************************************************************************************************
 ************************************************************************************************************
 **                                                                                                        **
 ** Copyright (c) 2008, Joshua Bettigole                                                                   **
 ** All rights reserved.                                                                                   **
 **                                                                                                        **
 ** Redistribution and use in source and binary forms, with or without modification, are permitted         **
 ** provided that the following conditions are met:                                                        **
 **                                                                                                        **
 ** - Redistributions of source code must retain the above copyright notice, this list of conditions       **
 **   and the following disclaimer.                                                                        **
 ** - Redistributions in binary form must reproduce the above copyright notice, this list of               **
 **   conditions and the following disclaimer in the documentation and/or other materials provided         **
 **   with the distribution.                                                                               **
 ** - The names of its contributors may not be used to endorse or promote products derived from this       **
 **   software without specific prior written permission.                                                  **
 **                                                                                                        **
 ** THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR         **
 ** IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND       **
 ** FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR              **
 ** CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL      **
 ** DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,      **
 ** DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER     **
 ** IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT      **
 ** OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                        **
 **                                                                                                        **
 ************************************************************************************************************
 ************************************************************************************************************/

//gemini-2013
//if(!$__datetimeselect)
//{
//	$__datetimeselect = 1;

	class MyDate
	{ 
	
		private $month_arr = array(
						1=>array('Jan','January'),
						2=>array('Feb','February'),
						3=>array('Mar','March'),
						4=>array('Apr','April'),
						5=>array('May','May'),
						6=>array('June','June'),
						7=>array('July','July'),
						8=>array('Aug','August'),
						9=>array('Sept','September'),
						10=>array('Oct','October'),
						11=>array('Nov','November'),
						12=>array('Dec','December')
					);
	
		private $day_arr = array(
						1=>array('Mon','Monday'),
						2=>array('Tue','Tuesday'),
						3=>array('Wed','Wednesday'),
						4=>array('Thu','Thursday'),
						5=>array('Fri','Friday'),
						6=>array('Sat','Saturday'),
						7=>array('Sun','Sunday')
					);
		
		private $suffix = array('st','nd','rd','th');
	
		private $selected;
		private $fieldname;
		private $yearstart = 0;
		private $yearend = 9;
		private $yearrev = 0;
		public $selectbox;
                
	
		public function __construct($format,$fieldname="datetime",$selected="",$range=0,&$my_selectbox)
		{
			//gemini-2013
                        $escape=false;
                        //
                        $this->selectbox = "";
			if(is_array($selected)) $this->selected = $selected;
			$this->fieldname = $fieldname;
			if($range)
			{
				if(strpos($range,"-")!==false)
				{
					$rangearr = explode("-",$range,2);
					if(intval($rangearr[0]) > intval($rangearr[1]))
					{
						$this->yearstart = (intval($rangearr[1]) - intval(date('Y')));
						$this->yearend = (intval($rangearr[0]) - intval(date('Y')));
						$this->yearrev = 1;
					}
					else
					{
						$this->yearstart = (intval($rangearr[0]) - intval(date('Y')));
						$this->yearend = (intval($rangearr[1]) - intval(date('Y')));
					}
				}
				else if(is_numeric($range))
					$this->yearend = ($range-1);
			}
			foreach(str_split($format) as $field)
			{
				if($escape == true)
				{
					$this->selectbox .= $field;
					$escape = false;
				}
				else
				{
					switch($field)
					{
					// Day
						case 'd':
							if(!is_array($selected) && $selected) $this->selected['day'] = date('d',$selected);
							$this->selectbox .= $this->buildDayByNumeric($field);
							break;
						case 'j':
							if(!is_array($selected) && $selected) $this->selected['day'] = date('j',$selected);
							$this->selectbox .= $this->buildDayByNumeric($field);
							break;
						case 'N':
							if(!is_array($selected) && $selected) $this->selected['dow'] = date('N',$selected);
							$this->selectbox .= $this->buildDOWByNumeric($field);
							break;
						case 'w':
							if(!is_array($selected) && $selected) $this->selected['dow'] = date('w',$selected);
							$this->selectbox .= $this->buildDOWByNumeric($field);
							break;
						case 'D':
							if(!is_array($selected) && $selected) $this->selected['dow'] = date('D',$selected);
							$this->selectbox .= $this->buildDOWByText($field);
							break;
						case 'l':
							if(!is_array($selected) && $selected) $this->selected['dow'] = date('l',$selected);
							$this->selectbox .= $this->buildDOWByText($field);
							break;
						case 'S':
							if(!is_array($selected) && $selected) $this->selected['suffix'] = date('S',$selected);
							$this->selectbox .= $this->buildSuffix();
							break;
						case 'z':
							if(!is_array($selected) && $selected) $this->selected['doy'] = date('z',$selected);
							$this->selectbox .= $this->buildDOY();
							break;
					// Week
						case 'W':
							if(!is_array($selected) && $selected) $this->selected['woy'] = date('W',$selected);
							$this->selectbox .= $this->buildWOY();
							break;
					// Month
						case 'm':
							if(!is_array($selected) && $selected) $this->selected['month'] = date('m',$selected);
							$this->selectbox .= $this->buildMonthByNumeric($field);
							break;
						case 'n':
							if(!is_array($selected) && $selected) $this->selected['month'] = date('n',$selected);
							$this->selectbox .= $this->buildMonthByNumeric($field);
							break;
						case 'F':
							if(!is_array($selected) && $selected) $this->selected['month'] = date('F',$selected);
							$this->selectbox .= $this->buildMonthByText($field);
							break;
						case 'M':
							if(!is_array($selected) && $selected) $this->selected['month'] = date('M',$selected);
							$this->selectbox .= $this->buildMonthByText($field);
							break;
						case 't':
							if(!is_array($selected) && $selected) $this->selected['dim'] = date('t',$selected);
							$this->selectbox .= $this->buildDaysInMonth();
							break;
					// Year
						case 'Y':
							if(!is_array($selected) && $selected) $this->selected['year'] = date('Y',$selected);
							$this->selectbox .= $this->buildYear($field);
							break;
						case 'o':
							if(!is_array($selected) && $selected) $this->selected['year'] = date('o',$selected);
							$this->selectbox .= $this->buildYear($field);
							break;
						case 'y':
							if(!is_array($selected) && $selected) $this->selected['year'] = date('y',$selected);
							$this->selectbox .= $this->buildYear($field);
							break;
						case 'L':
							if(!is_array($selected) && $selected) $this->selected['leap'] = date('L',$selected);
							$this->selectbox .= $this->buildLeapYear();
							break;
					// Time
						case 'a':
						case 'A':
							if(!is_array($selected) && $selected) $this->selected['ampm'] = date('a',$selected);
							$this->selectbox .= $this->buildAMPM($field);
							break;
						case 'B':
							if(!is_array($selected) && $selected) $this->selected['swatch'] = date('B',$selected);
							$this->selectbox .= $this->buildSwatch();
							break;
						case 'g':
						case 'h':
							if(!is_array($selected) && $selected) $this->selected['hour'] = date('g',$selected);
							$this->selectbox .= $this->buildHour($field);
							break;
						case 'G':
						case 'H':
							if(!is_array($selected) && $selected) $this->selected['hour'] = date('G',$selected);
							$this->selectbox .= $this->buildHour($field);
							break;
						case 'i':
							if(!is_array($selected) && $selected) $this->selected['minute'] = date('i',$selected);
							$this->selectbox .= $this->buildMinute();
							break;
						case 's':
							if(!is_array($selected) && $selected) $this->selected['second'] = date('s',$selected);
							$this->selectbox .= $this->buildSecond();
							break;
					// Timezone
						case 'e':
							if(!is_array($selected) && $selected) $this->selected['timezone'] = date('e',$selected);
							$this->selectbox .= $this->buildTimeZoneText($field);
							break;
						case 'T':
							if(!is_array($selected) && $selected) $this->selected['timezone'] = date('T',$selected);
							$this->selectbox .= $this->buildTimeZoneText($field);
							break;
						case 'P':
							if(!is_array($selected) && $selected) $this->selected['tzoffset'] = date('P',$selected);
							$this->selectbox .= $this->buildTimeZoneOffset($field);
							break;
						case 'O':
							if(!is_array($selected) && $selected) $this->selected['tzoffset'] = date('O',$selected);
							$this->selectbox .= $this->buildTimeZoneOffset($field);
							break;
						case 'Z':
							if(!is_array($selected) && $selected) $this->selected['tzoffset'] = date('Z',$selected);
							$this->selectbox .= $this->buildTimeZoneOffset($field);
							break;
						case 'I':
							if(!is_array($selected) && $selected) $this->selected['dst'] = date('I',$selected);
							$this->selectbox .= $this->buildDST($field);
							break;
					// Presets
						case 'c':
							if(!is_array($selected) && $selected)
							{
								$this->selected['year'] = date('Y',$selected);
								$this->selected['month'] = date('m',$selected);
								$this->selected['day'] = date('d',$selected);
								$this->selected['hour'] = date('H',$selected);
								$this->selected['minute'] = date('i',$selected);
								$this->selected['second'] = date('s',$selected);
								$this->selected['tzoffset'] = date('P',$selected);
							}
							//gemini-2013
                                                        //$this->selectbox .= $this->buildYear('Y')."-".$this->buildMonthByNumeric('m')."-".$this->buildDayByNumeric('d')."T".$this->buildHour('H').":".$this->buildMinute().":".$this->buildSecond().$this->buildTimeZoneOffset('P');
                                                        $this->selectbox .= $this->buildYear('Y')."-".$this->buildMonthByNumeric('m')."-".$this->buildDayByNumeric('d')."T".$this->buildHour('H').":".$this->buildMinute();
                                                        
					// Otherwise
						case "\\":
							$escape=true;
							break;
						case ' ':
							$this->selectbox .= '&nbsp;';
							break;
						default:
							if(ord($field) < 32)
							{
								switch(ord($field))
								{
									case 9:
										$this->selectbox .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										break;
									case 10:
									case 13:
										$this->selectbox .= '<br/>';
										break;
								}
							}
							else
							{
								$this->selectbox .= $field;
							}
							break;
					}
					//print ord($field)."<br/>";
				}
			}
                        //gemini-2013
			//echo $this->selectbox;
			$my_selectbox=$this->selectbox;
                        return;                        
		}
	
		
	/*********************************************************************************
		* Day Functions                                                                 *
		*********************************************************************************/
		
		private function buildDOWByNumeric($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[dow]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=0;$v<=6;$v++)
			{
				$s = (intval($this->selected['dow']) == $v) ? ' selected="selected"' : '';
				$value = ($field == 'w') ? $v : $v+1;
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
		private function buildDOWByText($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[dow]">'."\n";
			$string .= '<option value=""></option>'."\n";
			foreach($this->day_arr as $k => $v)
			{
				$s = (strtolower($this->selected['dow']) == strtolower($v[0]) || strtolower($this->selected['dow']) == strtolower($v[1])) ? ' selected="selected"' : '';
				$value = ($field == 'l') ? $v[1] : $v[0];
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
		private function buildDayByNumeric($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[day]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=1;$v<=31;$v++)
			{
				$s = (intval($this->selected['day']) == $v) ? ' selected="selected"' : '';
				$value = ($field == 'd') ? sprintf("%02d",$v) : $v;
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildSuffix()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[suffix]">'."\n";
			$string .= '<option value=""></option>'."\n";
			foreach($this->suffix as $v)
			{
				$s = (strtolower($this->selected['suffix']) == strtolower($v)) ? ' selected="selected"' : '';
				$string .= '<option value="'.$v.'"'.$s.'>'.$v.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
		private function buildDOY()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[doy]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=0;$v<=365;$v++)
			{
				$s = (intval($this->selected['doy']) == $v) ? ' selected="selected"' : '';
				$string .= '<option value="'.$v.'"'.$s.'>'.$v.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
		
	/*********************************************************************************
	* Week Functions                                                                *
	*********************************************************************************/
		
		private function buildWOY()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[woy]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=1;$v<=52;$v++)
			{
				$s = (intval($this->selected['woy']) == $v) ? ' selected="selected"' : '';
				$string .= '<option value="'.$v.'"'.$s.'>'.$v.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
	
	/*********************************************************************************
	* Month Functions                                                               *
	*********************************************************************************/
	
		private function buildMonthByNumeric($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[month]">'."\n";
			$string .= '<option value=""></option>'."\n";
			foreach($this->month_arr as $k => $v)
			{
				$s = ($this->selected['month'] == $k) ? ' selected="selected"' : '';
				$value = ($field == 'm') ? sprintf("%02d",$k) : $k;
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
		private function buildMonthByText($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[month]">'."\n";
			$string .= '<option value=""></option>'."\n";
			foreach($this->month_arr as $k => $v)
			{
				$s = (strtolower($this->selected['month']) == strtolower($v[0]) || strtolower($this->selected['month']) == strtolower($v[1])) ? ' selected="selected"' : '';
				$value = ($field == 'F') ? $v[1] : $v[0];
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildDaysInMonth()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[dim]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=28;$v<=31;$v++)
			{
				$s = (intval($this->selected['dim']) == $v) ? ' selected="selected"' : '';
				$string .= '<option value="'.$v.'"'.$s.'>'.$v.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
	
	/*********************************************************************************
	* Year Functions                                                                *
	*********************************************************************************/
	
		private function buildLeapYear()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[leap]">'."\n";
			$s = (intval($this->selected['leap']) != '1') ? ' selected="selected"' : '';
			$string .= '<option value="0"'.$s.'>No</option>'."\n";
			$s = (intval($this->selected['leap']) == '1') ? ' selected="selected"' : '';
			$string .= '<option value="1"'.$s.'>Yes</option>'."\n";
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildYear($field)
		{
			$s = '';
			$sstring = '';
			$string = '<select name="'.$this->fieldname.'[year]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=$this->yearstart;$v<=$this->yearend;$v++)
			{
				$value = (intval(date($field)) + $v);
				$s = (intval($this->selected['year']) == $value) ? ' selected="selected"' : '';
				$value = ($field == 'y') ? sprintf("%02d",$value) : $value;
				if($this->yearrev)
					$sstring = '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n" . $sstring;
				else
					$sstring .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";
			}
			$string .= $sstring;
			$string .= '</select>'."\n";
			return $string;
		}
	
	
	/*********************************************************************************
	* Time Functions                                                                *
	*********************************************************************************/
	
		private function buildAMPM($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[ampm]">'."\n";
			$string .= '<option value=""></option>'."\n";
			$s = (strtolower($this->selected['ampm']) == 'am') ? ' selected="selected"' : '';
			$value = $field == 'A' ? 'AM' : 'am';
			$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";
			$s = (strtolower($this->selected['ampm']) == 'pm') ? ' selected="selected"' : '';
			$value = $field == 'A' ? 'PM' : 'pm';
			$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildSwatch()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[swatch]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=0;$v<=999;$v++)
			{
				$s = (intval($this->selected['swatch']) == $v) ? ' selected="selected"' : '';
				$value = sprintf("%03d",$v);
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildHour($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[hour]">'."\n";
			$string .= '<option value=""></option>'."\n";
			$start = $field == 'G' || $field == 'H' ? 0 : 1;
			$end = $field == 'G' || $field == 'H' ? 23 : 12;
			for($v=$start;$v<=$end;$v++)
			{
				$s = (intval($this->selected['hour']) == $v) ? ' selected="selected"' : '';
				$value = $field == 'h' || $field == 'H' ? sprintf("%02d",$v) : $v;
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildMinute()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[minute]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=0;$v<=59;$v++)
			{
				$s = (intval($this->selected['minute']) == $v) ? ' selected="selected"' : '';
				$value = sprintf("%02d",$v);
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildSecond()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[second]">'."\n";
			$string .= '<option value=""></option>'."\n";
			for($v=0;$v<=59;$v++)
			{
				$s = (intval($this->selected['second']) == $v) ? ' selected="selected"' : '';
				$value = sprintf("%02d",$v);
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
	
	
	/*********************************************************************************
	* TimeZone Functions                                                            *
	*********************************************************************************/
		
		private function buildTimeZoneText($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[timezone]">'."\n";
			$string .= '<option value=""></option>'."\n";
			$tzarray = $field == 'T' ? timezone_abbreviations_list() : timezone_identifiers_list();
			foreach($tzarray as $k=>$v)
			{
				$value = ($field == 'T') ? strtoupper($k) : $v;
				$s = ($this->selected['timezone'] == $value) ? ' selected="selected"' : '';
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";     
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildTimeZoneOffset($field)
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[tzoffset]">'."\n";
			$string .= '<option value=""></option>'."\n";
			$tzarray = timezone_abbreviations_list();
			$offsetarray = array();
			foreach($tzarray as $tzsubarray)
				foreach($tzsubarray as $tzset)
					$offsetarray[] = $tzset['offset'];
			$offsetarray = array_unique($offsetarray);
			sort($offsetarray);
			foreach($offsetarray as $offset)
			{
				if($field == 'Z')
				{
					$value = $offset;
				}
				else
				{
					$format = ($field == 'P') ? 'H:i' : 'Hi';
					$value = ($offset < 0) ? sprintf('-%04s',date($format,mktime(0,0,($offset*-1)))) : sprintf('+%04s',date($format,mktime(0,0,$offset)));
				}
				$s = ($this->selected['tzoffset'] == $value) ? ' selected="selected"' : '';
				//$value = ($field == 'P') ? sprintf("-%04d",$v) : $v;
				$string .= '<option value="'.$value.'"'.$s.'>'.$value.'</option>'."\n";
			}
			$string .= '</select>'."\n";
			return $string;
		}
		
		private function buildDST()
		{
			$s = '';
			$string = '<select name="'.$this->fieldname.'[dst]">'."\n";
			$s = (intval($this->selected['dst']) != '1') ? ' selected="selected"' : '';
			$string .= '<option value="0"'.$s.'>No</option>'."\n";
			$s = (intval($this->selected['dst']) == '1') ? ' selected="selected"' : '';
			$string .= '<option value="1"'.$s.'>Yes</option>'."\n";
			$string .= '</select>'."\n";
			return $string;
		}
		
	}
//}
?>