<?php

/*

4
8
16
32
64
128
256
512
1024
2048

*/

$seed = "1234567890";
$width = "100";
$length = "255";
$height = "255";
$map = array();
$end = 9;

$terrain[] = "earth";
$terrain[] = "air";


function variate($a, $b, $i=1){
  // Choose one of the variation Functions
  return variate_two($a, $b, $i=1);
}

function variate_one(){
  // Variation one: A more or less random number between these two
  $x = rand(-1, 1) + $a;
  $y = rand(-1, 1) + $b;
  return rand($x, $y);
}

function variate_two($a, $b, $i=1){
  $mid = ceil(($a + $b) / 2);
  $j = 64;
  while($i>0){
    $j = $j / 2;
    $i--;
  }
  $x = $mid - $j;
  $y = $mid + $j;
  return rand($x, $y);
}



function create_random_pane($height){
  $pane[0][0] = variate(0, $height);
  $pane[0][1] = variate(0, $height);
  $pane[1][0] = variate(0, $height);
  $pane[1][1] = variate(0, $height);
  return $pane;
}

function diversify_pane($smallpane, $i=1){
  $largepane = array();
  // First set the easy ones
  $largepane[0][0] = $smallpane[0][0];
  $largepane[0][1] = 0;
  $largepane[0][2] = $smallpane[0][1];
  $largepane[1][0] = 0;
  $largepane[1][1] = 0;
  $largepane[1][2] = 0;
  $largepane[2][0] = $smallpane[1][0];
  $largepane[2][1] = 0;
  $largepane[2][2] = $smallpane[1][1];
  
  // Then randomize the easy ones
  $largepane[0][1] = variate($smallpane[0][0], $smallpane[0][1], $i);
  $largepane[1][0] = variate($smallpane[0][0], $smallpane[1][0], $i);
  $largepane[1][2] = variate($smallpane[0][1], $smallpane[1][1], $i);
  $largepane[2][1] = variate($smallpane[1][0], $smallpane[1][1], $i);
  
  // Then randomize the last one
  $one = variate($largepane[0][1], $largepane[2][1], $i);
  $two = variate($largepane[1][0], $largepane[1][2], $i);
  $largepane[1][1] = variate($one, $two);
 
  return $largepane;
}

function split_pane($pane){
  $result[] = get_smallpane_from_bigpane($pane, 0, 0);
  $result[] = get_smallpane_from_bigpane($pane, 0, 1);
  $result[] = get_smallpane_from_bigpane($pane, 1, 0);
  $result[] = get_smallpane_from_bigpane($pane, 1, 1);
  return $result;
}

function get_smallpane_from_bigpane($pane, $offsetx, $offsety){
  $result = array();
  $result[0][0] = $pane[$offsetx+0][$offsety+0];
  $result[0][1] = $pane[$offsetx+0][$offsety+1];  
  $result[1][0] = $pane[$offsetx+1][$offsety+0];
  $result[1][1] = $pane[$offsetx+1][$offsety+1];  
  return $result;
}

function add_to_pane($result, $pane, $offsetx, $offsety){
  $x = $offsetx;
  foreach($pane as $line){
    $y = $offsety;
    foreach($line as $point){
      $result[$x][$y] = $point;
      $y++;
    }
    $x++;
  }   
  return $result;
}

function merge_big_panes($fourpanes){
  $resultpane = array();
  $resultpane = add_to_pane($resultpane, $fourpanes[0], 0, 0);
  $resultpane = add_to_pane($resultpane, $fourpanes[1], 0, count($fourpanes[0])-1);
  $resultpane = add_to_pane($resultpane, $fourpanes[2], count($fourpanes[0])-1, 0);
  $resultpane = add_to_pane($resultpane, $fourpanes[3], count($fourpanes[0])-1, count($fourpanes[0])-1);
  return $resultpane;
}

function point2color_rgb($point){
  if($point < 32){
    $color = "30,144,255";
  }elseif($point < 64){
    $color = "135,206,250";
  }elseif($point < 96){
    $color = "238,232,170";
  }elseif($point < 128 ){
      $color = "154,205,50";
  }elseif($point < 192 ){
    $color = "0,100,0";
  }elseif($point < 224 ){
      $color = "128,128,128";
  }else{
    $color = "211,211,211";
  }
  return $color;
}

function point2color_names($point){
  if($point < 32){
    $color = "darkblue";
  }elseif($point < 64){
    $color = "lightblue";
  }elseif($point < 96){
    $color = "beige";
  }elseif($point < 128 ){
      $color = "lightgreen";
  }elseif($point < 192 ){
    $color = "darkgreen";
  }elseif($point < 224 ){
      $color = "darkgray";
  }else{
    $color = "lightgray";
  }
  return $color;
}


function print_map_canvas($map){
  print("<canvas id='mymap'>");
  print "<script>
    // Get a reference to the element.
    var elem = document.getElementById('mymap');

    // Always check for properties and methods, to make sure your code doesn't break 
    // in other browsers.
    if (elem && elem.getContext) {
      // Get the 2d context.
      // Remember: you can only initialize one context per element.
      var context = elem.getContext('2d');
      if (context) {
        // You are done! Now you can draw your first rectangle.
        // You only need to provide the (x,y) coordinates, followed by the width and 
        // height dimensions.
        // Create an ImageData object.
        var imgd = context.createImageData(".count($map).",".count($map).");
        var pix = imgd.data; ";
 
  $i = 0;
  $x = 1;
  foreach($map as $line){
    $y = 1;
    foreach ($line as $point){
      $color = point2color_rgb($point);      
      $colors = explode(",", $color);
      print "    pix[".$i."  ] = ".$colors[0]."; // red channel\n";
      $i++;
      print "    pix[".$i."  ] = ".$colors[1]."; // green channel\n";
      $i++;
      print "    pix[".$i."  ] = ".$colors[2]."; // blue channel\n";
      $i++;
      print "    pix[".$i."] = 127; // alpha channel\n";
      $i++;       
    }
  }
 
  print "// Draw the ImageData object at the given (x,y) coordinates.
        context.putImageData(imgd, 0,0);
        }
      }    
      </script>";
}

function print_map_table($map){
  print ("<style>td {font-family:monaco; font-size:8px; width:5px; height:5px;}</style>");
  print ("<style>table {border-collapse:collapse;}</style>");
  print("<table>");
  $i = 0;
  $x = 1;
  foreach($map as $line){
    print("<tr>");
    $y = 1;
    foreach ($line as $point){
      $color = point2color_names($point);
      print "<td style='background-color:".$color.";  width='5px' height='5px'><!-- ".$point."--></td>";
      $y++;
    }
    $x++;
    print ("</tr>");
  }
  print("</table>");
 
}


function print_map_image($map){
  // Set Header and Colors
  header("Content-type: image/png");
  $image=imagecreate(count($map)*4, count($map)*4);
  $colors["darkblue"]=imagecolorallocate($image, 30,144,255);
  $colors["lightblue"]=imagecolorallocate($image, 135,206,250);
  $colors["beige"]=imagecolorallocate($image, 238,232,170);
  $colors["lightgreen"]=imagecolorallocate($image, 154,205,50);
  $colors["darkgreen"]=imagecolorallocate($image, 0,100,0);
  $colors["darkgray"]=imagecolorallocate($image, 128,128,128);
  $colors["lightgray"]=imagecolorallocate($image, 211,211,211); 
  
  // Let's go.
  $i = 0;
  $x = 0;
  foreach($map as $line){   
    $y = 0;
    foreach ($line as $point){
      $color = point2color_names($point);
      imagerectangle ($image, $x*4, $y*4, $x*4+3, $y*4+3, $colors[$color]);
      imagerectangle ($image, $x*4+1, $y*4+1, $x*4+2, $y*4+2, $colors[$color]);
      $y++;
    }
    $x++;   
  }  
  
  // Print the Image
  imagepng($image);
  imagedestroy($image); 
}


function recursive($pane, $end, $i){
  if($end == $i){
    return $pane;
  }else{
    $i++;
    $pane = diversify_pane($pane, $i);
    $panes = split_pane($pane);
    $fourpanes = array();
    foreach($panes as $apane){   
      $fourpanes[] = recursive($apane, $end, $i);
    }
    $result = merge_big_panes($fourpanes);
    return $result;
  }  
}

$map = create_random_pane($height);
$map = recursive($map, 7, $i);
print_map_image($map);









?>

