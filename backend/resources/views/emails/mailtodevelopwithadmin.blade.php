<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
	<div style="width:100%; height:auto; margin:20px auto; border:2px solid #e9e9e9; border-radius:5px; font-size:14px;">
		<div style="width:100%; height:55px; background:#4c5e70; border-radius:5px 5px 0 0;">
		</div>
		<div style="width:97%; height:auto; margin:15px 0 0 27px; color:#727272; line-height:35px;">
	        <b><?php echo $body["lang1"];?>:</b>
	        <div style="text-indent: 2em;"><?php if($flag == "agree"){echo $body["lang2"];}else{echo $body["lang3"];}?></div>
	        <div style="text-align:right;margin-right: 50px;"><?php echo $body["lang4"];?></div>
	        <div style="text-align:right;margin-right: 50px;"><?php echo $time;?></div>
	    </div>
	</div>
</html>