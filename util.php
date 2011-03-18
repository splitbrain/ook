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

/******************************************************
  Some tools which I created and you might find useful
 ******************************************************/

/*
  fuck_text() generates brainfuck code from $text. The resulting code will use the current
  register p for looping and the register p+1 for the resulting character. Thus, make sure
  these two registers are zero (prepend "[-]>[-]<<" to clear the first two registers).
  
  I suggest you to use this function in conjunction with wordwrap:
  
    $bf = wordwrap(fuck_text("Hello World"), 75, "\n", 1));
	
  wich will generate nice, formatted output.
*/

function fuck_text($text) {
  /* value of current pointer */
  $value = 0;

  for($_t = 0; $_t < strlen($text); ++$_t) {

	/* ordinal difference between current char and the one we want to have */
	$diff  = ord($text[$_t]) - $value;

	/* it's easier like this than always computing this value - saves some cpu cycles*/
	$value = ord($text[$_t]);

	/* repeat current character */
	if($diff == 0) {
      $result .= ">.<";
	  continue;
	}

    /* is it worth making a loop? 
	   No. A bunch of + or - consume less space than the loop. */
    if(abs($diff) < 10) {

      /* output a bunch of + or - */
	  if($diff > 0)
	    $result .= ">" . str_repeat("+", $diff);
	  else if($diff < 0)
	    $result .= ">" . str_repeat("-", abs($diff));

    }
	/* Yes, create a loop. This will make the resulting code more compact. */
	else { 

	  /*  we strictly use ints, as PHP has some bugs with floating point operations 
	     (even if no division is involved) */
	  $loop = (int)sqrt(abs($diff));

      /* set loop counter */
      $result .= str_repeat("+", $loop);

	  /* execute loop, then add reminder */
	  if($diff > 0) {
		 $result .= "[->" . str_repeat("+", $loop) . "<]";
         $result .= ">" . str_repeat("+", $diff - pow($loop, 2));
      }
      else if($diff < 0) {
	     $result .= "[->" . str_repeat("-", $loop) . "<]";
         $result .= ">" . str_repeat("-", abs($diff) - pow($loop, 2));
	  }

   } /* end: if loop */

	  $result .= ".<";

  } /* end: for */

  /* cleanup */
  return str_replace("<>", "", $result);
}

/*
  This function checks whether the brackets are balanced. Make sure you get the
  return value correctly, as 0 means "OK" and not "false":

    positive return value : this many [ too much = this many ] missing
	zero                  : balanced
    negatice return value : this many ] too much = this many [ missing
*/

function bf_brackets_balance($bf) {
  $histogram = count_chars($bf);
  return $histogram[91] - $histogram[93];
}

/*
  This returns the real number of brainfuck commands when all other characters
  are ignored.
*/

function bf_count_chars($bf) {
  return count(preg_split("/[\[\]+\-<>.,]/", $bf)) - 1;
}

/*
  bf_reduce() removes all unnecessary characters from a brainfuck source, only
  leaving actual instructions left.
*/

function bf_reduce($bf) {
  preg_match_all("/[\[\]+\-<>.,]/", $bf, $matches);
  return join("", $matches[0]);
}

?>