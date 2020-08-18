<?php require_once('fdelix_HCADatabase.php'); ?> 
<?php

$mocolor1 = "#C0C0C0";
$mocolor2 = "#FFFFFF";
$mocolor3 = "#FF9933";
$mocolor = $mocolor1;

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($dbc, $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($dbc, $theValue) : mysqli_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

// *** Redirect if key exists on insert
if(isset($_POST["MM_insert"])) 
	$FX_action = "MM_insert";
 	$FX_found = false;
	if (isset($FX_action)) {
		$FX_dupKeyRedirect = "index.php";
		$FX_dupKeyValue = $_POST["Surname"].$_POST["Name"];
		$FX_dupKeySQL = "SELECT Redunt FROM anniv34 WHERE Redunt = '" . $FX_dupKeyValue . "'";
		mysqli_select_db($fdelix_HCADatabase, $database_fdelix_HCADatabase);
		$FX_rsKey=mysqli_query($fdelix_HCADatabase, $FX_dupKeySQL) or die(mysqli_error());
		if(mysqli_num_rows($FX_rsKey) > 0) {
	    // this value was found - can not execute action
    	$FX_found = true;
	    $FX_dupValue = "Redunt (" . $FX_dupKeyValue . ") ";
	}
	if($FX_found) {
    	$FX_dupValue .= "already exists!";
	    $FX_qsChar = "?";
    	if ($FX_dupKeyRedirect == "") $FX_dupKeyRedirect = substr($_SERVER["PHP_SELF"], strrpos($_SERVER["PHP_SELF"], "/")+1);
		    if (strpos($FX_dupKeyRedirect, "?")) $FX_qsChar = "&";
		    $FX_dupKeyRedirect = $FX_dupKeyRedirect . $FX_qsChar . "FX_RequestKey=" . urlencode($FX_dupValue);
		    header ("Location: " . $FX_dupKeyRedirect);
	    exit;
	 }
  mysqli_free_result($FX_rsKey);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2") && ($_POST["Surname"] != "") && ($_POST["Name"] != "")&& ($_POST["Ministry"] != "")&& ($_POST["BusService"] != "")&& ($_POST["Tshirt"] != "") ){
  $insertSQL = sprintf("INSERT INTO anniv34 (RcdCd, GrpID, Surname, Name, Ministry, BookDate, Paid, Redunt,ValTag,Endorser,BusService,Tshirt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($fdelix_HCADatabase, $_POST['RcdCd'], "int"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['GrpID'], "int"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['Surname'], "text"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['Name'], "text"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['Ministry'], "text"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['BookDate'], "date"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['Paid'], "int"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['Surname'].$_POST['Name'], "text"),
					   GetSQLValueString($fdelix_HCADatabase, $_POST['ValTag'], "int"),
                       GetSQLValueString($fdelix_HCADatabase, $_POST['endorse'], "text"),
					   GetSQLValueString($fdelix_HCADatabase, $_POST['BusService'], "text"),
				  	   GetSQLValueString($fdelix_HCADatabase, $_POST['Tshirt'], "text"));

  mysqli_select_db($fdelix_HCADatabase, $database_fdelix_HCADatabase);
  $Result1 = mysqli_query($fdelix_HCADatabase, $insertSQL) or die(mysqli_error($fdelix_HCADatabase));

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
 }


$selected = mysqli_select_db($fdelix_HCADatabase, $database_fdelix_HCADatabase);
$query_Recordset3 = "SELECT count(*) as CountAll  FROM anniv34 WHERE ValTag <>1";
$Recordset3 = mysqli_query($fdelix_HCADatabase, $query_Recordset3) or die(mysqli_error());
$row_Recordset3 = mysqli_fetch_assoc($Recordset3);
$totalRows_Recordset3 = mysqli_num_rows($Recordset3);

$colname_Recordset1 = "Surname";
if (isset($_GET['firstSort'])) {
  $colname_Recordset1 = $_GET['firstSort'];
}
if ($colname_Recordset1 == "RcdCd") {
  $xOrder =" DESC";
} else {
  if ($colname_Recordset1 == "default") {	
  	$colname_Recordset1 = "Surname, Name";
    $xOrder ="ASC";
  } else {
  	if ($colname_Recordset1 == "Surname") {	
  		$colname_Recordset1 = "Surname, Name";
  		$xOrder ="ASC";
	}
  }
}

mysqli_select_db($fdelix_HCADatabase, $database_fdelix_HCADatabase);
$query_Recordset1 = sprintf("SELECT *,  date_add(now(), interval 12 hour) as ngayon, if(locate(' ',Surname)<>0,CONCAT(UPPER(SUBSTRING(Surname,1,1)), LOWER(SUBSTRING(Surname,2,Locate(' ', Surname)-1)),  UPPER(SUBSTRING(Surname ,Locate(' ', Surname)+1,1)), LOWER(SUBSTRING(Surname,Locate(' ', Surname)+2))), CONCAT( UPPER(SUBSTRING(Surname,1,1)), LOWER(SUBSTRING(Surname,2))))  as sur, if(locate(' ',Name)<>0,CONCAT(UPPER(SUBSTRING(Name,1,1)), LOWER(SUBSTRING(Name,2,Locate(' ', Name)-1)),  UPPER(SUBSTRING(Name ,Locate(' ', Name)+1,1)), LOWER(SUBSTRING(Name,Locate(' ', Name)+2))), CONCAT( UPPER(SUBSTRING(Name,1,1)), LOWER(SUBSTRING(Name,2)))) as nam, if(Ministry='CFC-FFL Friend',concat(' (c/o ',Endorser,')'),'') as endo, upper(Ministry) as minx FROM anniv34 WHERE Surname <> ' ' and ValTag <> 1  ORDER BY $colname_Recordset1 $xOrder");
$Recordset1 = mysqli_query($fdelix_HCADatabase, $query_Recordset1) or die(mysqli_error());
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

mysqli_select_db($fdelix_HCADatabase, $database_fdelix_HCADatabase);
$query_Recordset4 = "SELECT *,  if(locate(' ',Surname)<>0,CONCAT(UPPER(SUBSTRING(Surname,1,1)), LOWER(SUBSTRING(Surname,2,Locate(' ', Surname)-1)),  UPPER(SUBSTRING(Surname ,Locate(' ', Surname)+1,1)), LOWER(SUBSTRING(Surname,Locate(' ', Surname)+2))), CONCAT( UPPER(SUBSTRING(Surname,1,1)), LOWER(SUBSTRING(Surname,2))))  as sur, if(locate(' ',Name)<>0,CONCAT(UPPER(SUBSTRING(Name,1,1)), LOWER(SUBSTRING(Name,2,Locate(' ', Name)-1)),  UPPER(SUBSTRING(Name ,Locate(' ', Name)+1,1)), LOWER(SUBSTRING(Name,Locate(' ', Name)+2))), CONCAT( UPPER(SUBSTRING(Name,1,1)), LOWER(SUBSTRING(Name,2)))) as nam FROM anniv34 WHERE Ministry <> 'CFC-FFL Friend' ORDER BY Surname, Name ASC";
$Recordset4 = mysqli_query($fdelix_HCADatabase, $query_Recordset4) or die(mysqli_error());
$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
$totalRows_Recordset4 = mysqli_num_rows($Recordset4);

function makeStamp($theString) {
  if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $theString, $strReg)) {
    $theStamp = mktime($strReg[4],$strReg[5],$strReg[6],$strReg[2],$strReg[3],$strReg[1]);
  } else if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $theString, $strReg)) {
    $theStamp = mktime(0,0,0,$strReg[2],$strReg[3],$strReg[1]);
  } else if (preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $theString, $strReg)) {
    $theStamp = mktime($strReg[1],$strReg[2],$strReg[3],0,0,0);
  }
  return $theStamp;
}

function makeDateTime($theString, $theFormat) {
  $theDate=date($theFormat, makeStamp($theString));
  return $theDate;
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>MFC Hong Kong 34th Year Anniversary</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<script language="JavaScript" >
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) });
	var m =	$('tr:visible').length;
	$("p").text((m-8) + " Record(s) Found" );
  });

});


function valIndex() {
	if (document.form2.Surname.value == "" || document.form2.Name.value == "" ||document.form2.Ministry.value == "" ||document.form2.Tshirt.value == "" ){
	alert ('All fields need to be filled up Brother/Sister!');	
	return false;
	}else{
		if (document.form2.Ministry.value == "MFC Friend" & document.form2.endorse.value == "1"){
        	alert ('Please choose a MFC Host');	
			return false;
		}else if (document.form2.BusService.value == ""){
        	alert ('E-mail Address is required');	
			return false;		
		}else if (document.form2.Tshirt.value == ""){
        	alert ('Contact Number is required');	
			return false;		
		}else{
			if(!confirm("Hit 'OK' to Confirm, or 'Cancel' to go back and make changes.")){
			return false;
		    }
		}
	}		  
}
function endorser(a) {
	if (a == "MFC Friend"){
	document.form2.endorse.style.display = "block";
}else{
	document.form2.endorse.style.display = "none";

	}		  
}
</script>
<script language=JavaScript>

$(document).ready(function(){
    $(".close").click(function(){
        $("#myAlert").alert("close");
    });
});

function clickIE4(){
if (event.button==2){
return false;
}
}

function clickNS4(e){
if (document.layers||document.getElementById&&!document.all){
if (e.which==2||e.which==3){
return false;
}
}
}

if (document.layers){
document.captureEvents(Event.MOUSEDOWN);
document.onmousedown=clickNS4;
}
else if (document.all&&!document.getElementById){
document.onmousedown=clickIE4;
}

document.oncontextmenu=new Function("return false")

</script>
<style>
.jumbotron {
	background-color: #F39D3F; /* Orange */
	color: #ffffff;
}
.imgbk{
	background-image:url(logosmall1.png);
	background-repeat: no-repeat;
	background-position: center center;
}
</style>
<body style="padding-top:3em">
<div class="container-fluid text-center">    
	<div class="row content">
	  <div class="col-sm-3 sidenav">
       <img class="img-responsive" src="sweetlife.png"  width="466">
		<div class="well">
<form class="form-horizontal" action="index.php" method="post" name="form2" id="form2">
	  <table class="table table-hover">
			  <tbody>
					<tr>
			           <td align="center" style="color:#FFA500"><h3>Registration is now OPEN</h3></td>
				    </tr>
		    </tbody>
		</table>
			  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
					<input type="text" class="form-control" id="Name" placeholder="First Name" name="Name">
		</div>
				<div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
						<input type="text" class="form-control" id="Surname" placeholder="Last Name" name="Surname">
				</div>
				<div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-heart-empty"></i></span>
<select class="form-control" name="Ministry" id="Ministry" onChange="endorser(value)" >
						<option value="" selected="selected">Select Section</option>
						<option value="Couples">Couples</option>
						<option value="Singles">Singles</option>
						<option value="Handmaids">Handmaids</option>
						<option value="Servants">Servants</option>
						<option value="Youth">Youth</option>
						<option value="Kids">Kids</option>
						<option value="Kids Aged 3 and Below">Kids Aged 3 and Below</option>
						<option value="MFC Friend">MFC Friend</option>
				  </select>
                        <select name="endorse" class="form-control" id="endorse" style="display:none">
		                <option value="1">Select MFC Host</option>
        		        <?php do {  ?>
							<option value="<?php echo $row_Recordset4['sur']?>, <?php echo $row_Recordset4['nam']?>"><?php echo $row_Recordset4['sur']?>, <?php echo $row_Recordset4['nam']?></option>
		                <?php } while ($row_Recordset4 = mysqli_fetch_assoc($Recordset4));
							$rows = mysqli_num_rows($Recordset4);
							if($rows > 0) {
								mysqli_data_seek($Recordset4, 0);
								$row_Recordset4 = mysqli_fetch_assoc($Recordset4);
							} ?>
						</select>
				</div>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
						<input type="text" class="form-control" id="Tshirt" placeholder="Mobile Phone" name="Tshirt">
				</div>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
						<input type="text" class="form-control" id="BusService" placeholder="E-Mail Address" name="BusService">
				</div>

				<input name="Submit" type="submit" value="Save Entry" onClick="return valIndex()" class="btn btn-primary"  style="float:right; margin-top:2em; margin-bottom:2em" title="Registration is now OPEN"/>
                <input name="ValTag" type="hidden" id="ValTag" value="0" />
                <input type="hidden" name="RcdCd" value="" />
                <input type="hidden" name="GrpID" value="0"/>
                <input type="hidden" name="BookDate" value="<?php echo date('Y-m-d', strtotime(date('Y-m-d') . ' + 12 hours')); ?>" />
                <input name="Paid" type="hidden" value="0" />
                <input type="hidden" name="Redunt" value="" />
                <input type="hidden" name="MM_insert" value="form2" />
		  </form><br>
	  <table class="table table-hover">
			  <tbody>
					<tr align="center">
			           <td align="center"><span class="badge"><?php echo $row_Recordset3['CountAll']; ?></span>&nbsp;&nbsp;Latest # of Registrants </td>
				    </tr>
                    <tr>
                    <td align="center" colspan="2" class="small"> "MAKE ALL YOUTH KNOW CHRIST"</td>
                    </tr>
                    
		    </tbody>
		</table>

		</div>
	  </div><br>     
<div class="col-sm-7 text-left"> 
  			<input class="form-control" id="myInput" type="text" placeholder="Search.."><br><p style="font-size:12px; font-weight:bold; font-style:italic" align="right"></p>
            <form id="form1" name="form1" method="get" action="" class="form-horizontal">
                    <div class="input-group">
                     <span class="input-group-addon"><i class="glyphicon glyphicon-sort-by-alphabet"></i></span>
						<select class="form-control" name="firstSort" id="firstSort" onChange="submit()">
                        	<option value="default" selected="selected">-- Please Sort By --</option>
						  <option value="Surname" <?php if (!(strcmp("Surname", $_GET['firstSort']))) {echo "selected=\"selected\"";} ?>>Surname</option>
						  <option value="Name" <?php if (!(strcmp("Name", $_GET['firstSort']))) {echo "selected=\"selected\"";} ?>>Name</option>
						  <option value="Ministry" <?php if (!(strcmp("Ministry", $_GET['firstSort']))) {echo "selected=\"selected\"";} ?>>Section</option>
<option value="RcdCd" <?php if (!(strcmp("RcdCd", $_GET['firstSort']))) {echo "selected=\"selected\"";} ?>>Registered Date [New on Top]</option>
			          </select>
              </div>    
      </form>	
  		
      <div class="table-responsive">
        <table class="table table-striped" >
          <thead>
			<tr>
				<td height="23" colspan="2" align="left"><strong>Name</strong></td>
				  <td align="left"><strong>Ministry</strong></td>
					<td align="center" nowrap="nowrap"><strong>Reg. Date</strong></td>
   				</tr>
           </thead> 
			<?php do { ?>
               <tbody id="myTable">
				<tr>
					<td><?php echo $row_Recordset1['sur']; ?></td>
			        <td><?php echo $row_Recordset1['nam']; ?></td>
			        <td nowrap><div align="left"> <?php echo $row_Recordset1['Ministry']; ?><?php echo $row_Recordset1['endo']; ?></div></td>
			        <td align="center" nowrap="nowrap"><?php echo makeDateTime($row_Recordset1['BookDate'], 'd-m-Y'); ?></td>
                 </tr>
				</tbody>	
			<?php } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?> 
		</table>
      </div>
        
      
	  </div>
  <div class="col-sm-2 sidenav">

	</div>
</div>
<div align="center"><img src="mfc-logo.png" width="66" height="68"></div>
<div align="center" >
  Missionary Families of Christ<br>
    <p class="small">Australia <?php echo date("Y"); ?></p>
</div>
</body>
</html>
<?php
mysqli_free_result($Recordset1);
mysqli_free_result($Recordset3);
mysqli_free_result($Recordset4);

?>
