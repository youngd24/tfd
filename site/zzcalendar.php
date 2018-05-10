<?

// PHP Calendar Class
//  
// Copyright David Wilkinson 2000. All Rights reserved.
// 
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head 
// of the file.
//
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly 
// from the use of this script.
//
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk

class Calendar
{
    var $month;
    var $year;
    
    function Calendar($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }
   
    
    function getCalendarLink($month, $year)
    {
        return "";
    }
    
    function getDateLink($day, $month, $year)
    {
        return "javascript:overdate($day, $month, $year)";
    }
    

    function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
    
        $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
   
        $d = $days[$month - 1];
   
        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...
        
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }
    
        return $d;
    }


    function getHTML($future)
    {
        $s = "";
        
    	$daysInMonth = $this->getDaysInMonth($this->month, $this->year);
    	$date = getdate(mktime(12, 0, 0, $this->month, 1, $this->year));
    	
    	$first = $date["wday"];
    	$monthName = $date["month"];
    	
    	$prevMonth = $this->getCalendarLink($this->month - 1 >   0 ? $this->month - 1 : 12, $this->month - 1 >   0 ? $this->year : $this->year - 1);
    	$nextMonth = $this->getCalendarLink($this->month + 1 <= 12 ? $this->month + 1 :  1, $this->month + 1 <= 12 ? $this->year : $this->year + 1);
    	
    	$s .= "<table class=calendar width=180>\n";
    	$s .= "<tr><td colspan=7 align=center><font size=2><b>$monthName</b></td></tr>\n";
    	
    	$s .= "<tr>\n";
    	$s .= "<td align=center valign=top><b>S</b></td>\n";
    	$s .= "<td align=center valign=top><b>M</b></td>\n";
    	$s .= "<td align=center valign=top><b>T</b></td>\n";
    	$s .= "<td align=center valign=top><b>W</b></td>\n";
    	$s .= "<td align=center valign=top><b>T</b></td>\n";
    	$s .= "<td align=center valign=top><b>F</b></td>\n";
    	$s .= "<td align=center valign=top><b>S</b></td>\n";
    	$s .= "</tr>\n";
		$s .= "<tr><td colspan=7><img src=images/pixels/blackpixel.gif width=180 height=1></td></tr>\n";
    	
    	$d = 1 - $first;
    	   	
    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";       
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
    	        $s .= "<td align=center valign=top>";
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
					if (($d >= date("d") or $future == 1) and ($i != 0 and $i != 6)) {
						$link = $this->getDateLink($d, $this->month, $this->year);
						}
					else {
						$link = "";
					}
    	            $s .= (($link == "") ? $d : "<a href=\"$link\" onClick=switchbg(this)><font color=000000>$d</font></a>");
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";       
        	    $d++;
    	    }
    	    $s .= "</tr>\n";    
    	}
    	
    	$s .= "</table>\n";
    	
    	return $s;  	
    }
}


?>