<?php

/*

    Brainfuck interpreter in PHP
    Copyright (C) 2002 Daniel Lorch

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/


/* Debug function displays valuable debug information.
   Rewrite this if desired. 
   
   $s, $_s  Source string and pointer (current position)
   $d, $_d  Data   array  and pointer
   $i, $_i  Input  string and pointer
   $o       Output string
   
   */

function brainfuck_debug(&$s, &$_s, &$d, &$_d, &$i, &$_i, &$o) {
  echo "<table>\n";
  echo "<tr><td><b>Position</b></td><td><b>Value</b></td><td><b>ASCII</b></td></tr>\n";
  
  foreach($d as $element => $value) {
    echo "<tr>\n";
    echo "<td align=\"center\">" . $element . "</td>\n";
	echo "<td align=\"center\">" . ord($value) . "</td>\n";
	echo "<td align=\"center\">" . (ord($value) >= 32 ? htmlentities($value) : "&nbsp;") . "</td>\n";
	echo "</tr>\n";
  }
  
  echo "</table>\n";
}

/* The actual interpreter */

function brainfuck_interpret(&$s, &$_s, &$d, &$_d, &$i, &$_i, &$o) {
   do {
     switch($s[$_s]) {
	   /* Execute brainfuck commands. Values are not stored as numbers, but as their
	      representing characters in the ASCII table. This is perfect, as chr(256) is
		  automagically converted to chr(0). */
       case '+': $d[$_d] = chr(ord($d[$_d]) + 1); break;
	   case '-': $d[$_d] = chr(ord($d[$_d]) - 1); break;
       case '>': $_d++; if(!isset($d[$_d])) $d[$_d] = chr(0); break;
	   case '<': $_d--; break;
	   
       /* Output is stored in a variable. Change this to
	        echo $d[$_d]; flush();
		  if you would like to have a "live" output (when running long calculations, for example.
		  Or if you are just terribly impatient). */
	   case '.': $o .= $d[$_d]; break;
	   
	   /* Due to PHP's non-interactive nature I have the whole input passed over in a string. 
	      I successively read characters from this string and pass it over to BF every time a
		  ',' command is executed. */
	   case ',': $d[$_d] = $_i==strlen($i) ? chr(0) : $i[$_i++]; break;
	   
	   /* Catch loops */
	   case '[':
	     /* Skip loop (also nested ones) */
	     if((int)ord($d[$_d]) == 0) {
           $brackets = 1;
		   while($brackets && $_s++ < strlen($s)) {
		     if($s[$_s] == '[')
		       $brackets++;
			 else if($s[$_s] == ']')
			   $brackets--;
		   }
		 }
		 /* Execute loop */
		 else {
  	       $pos = $_s++-1;
		   /* The closing ] returns true when the loop has to be executed again. If so, then return
		      to the $pos(ition) where the opening [ is. */
	       if(brainfuck_interpret($s, $_s, $d, $_d, $i, $_i, $o))
	         $_s = $pos;
         }
	     break;
	   /* Return true when loop has to be executed again. It is redundant to the [ checking, but
	      it will save some parsing time (otherwise the interpreter would have to return to [ only
		  to skip all characters again) */
	   case ']': return ((int)ord($d[$_d]) != 0);
	   /* Call debug function */
	   case '#': brainfuck_debug($s, $_s, $d, $_d, $i, $_i, $o);
    }
  } while(++$_s < strlen($s));
}

/* Call this one in order to interpret brainfuck code */

function brainfuck($source, $input='') {

  /* Define needed variables:

     $data    Brainfuck's memory
	 $source  Source data
     $input   Simulate STDIN
	 $output  Save output in here
	 
	 Each with according index variables
  */
  
  $data         = array();
  $data[0]      = chr(0); /* It is necessary to set every element explicitly, as 
                             PHP treats arrays as hashes */
  $data_index   = 0;
  
  $source_index = 0;
  
  $input_index  = 0;
  
  $output       = '';
  
  /* Call the actual interpreter */
  brainfuck_interpret($source, $source_index,
                      $data,   $data_index,
					  $input,  $input_index,
					  $output);
  	
  return $output;
}

?>