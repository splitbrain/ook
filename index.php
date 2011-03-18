<?php
include "brainfuck.php";
include "util.php";
$input  = $_REQUEST['input'];
switch($_REQUEST['do']){
    case 'Text to Ook!':
    case 'Text to short Ook!':
        $output = fuck_text($input);
        $output = strtr($output,array('>' => 'Ook. Ook? ',
                                      '<' => 'Ook? Ook. ',
                                      '+' => 'Ook. Ook. ',
                                      '-' => 'Ook! Ook! ',
                                      '.' => 'Ook! Ook. ',
                                      ',' => 'Ook. Ook! ',
                                      '[' => 'Ook! Ook? ',
                                      ']' => 'Ook? Ook! ',
                                     ));
        if($_REQUEST['do'] == 'Text to short Ook!'){
            $output = str_replace('Ook','',$output);
            $output = str_replace(' ','',$output);
            $output = preg_replace('/(.....)/','\\1 ', $output);
        }
        $output = wordwrap($output,75,"\n");
        break;
    case 'Text to Brainfuck':
        $output = fuck_text($input);
        $output = preg_replace('/(.....)/','\\1 ', $output);
        $output = wordwrap($output,75,"\n");
        break;
    case 'Ook! to Text':
        $lookup = array(
                    '.?' => '>',
                    '?.' => '<',
                    '..' => '+',
                    '!!' => '-',
                    '!.' => '.',
                    '.!' => ',',
                    '!?' => '[',
                    '?!' => ']',
                  );

        $input = preg_replace('/[^\.?!]+/','',$input);
        $len = strlen($input);
        for($i=0;$i<$len;$i+=2){
            $output .= $lookup[$input{$i}.$input{$i+1}];
        }
        $output = brainfuck($output);
        break;
    case 'Brainfuck to Text':
        $output = brainfuck($input);
        break;
}

?>
<html>
<head>
    <title>Brainfuck/Text/Ook! obfuscator - deobfuscator. Decode and encode online.</title>

    <style>
        body {
            font-size: 80%;
            font-family: sans-serif;
        }
        a {
            text-decoration: none;
            color: #600;
        }
        textarea {
            border: solid 1px #000;
            width: 450px;
        }
        input, select {
            border: solid 1px #000;
        }
    </style>
</head>
<body>
<form action="" method="post">
<textarea name="input" cols="80" rows="10"><?php echo $output?></textarea><br />
<input type="submit" name="do" value="Text to Ook!" />
<input type="submit" name="do" value="Text to short Ook!" />&nbsp;&nbsp;&nbsp;
<input type="submit" name="do" value="Ook! to Text" /><br />
<input type="submit" name="do" value="Text to Brainfuck" />&nbsp;&nbsp;&nbsp;
<input type="submit" name="do" value="Brainfuck to Text" />
</form>

</body>
</html>
