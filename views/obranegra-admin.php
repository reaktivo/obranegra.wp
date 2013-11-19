<div id="obranegra-admin" class="wrap">

  <h2>Obra Negra</h2>
  <h3>
    <span style="color: <?=$background?>; background: <?=$foreground?>"> <?=$states_count?> / 256</span>
     〰 <?=$foreground?>:  <?=$background?>
  </h3>

  <form name="obranegra_form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
    <textarea id="obranegra_css" name="obranegra_css"><?=$latest_css->text?></textarea>
		<p class="submit">
		  <input type="submit" name="Submit" value="Save State" />
		</p>
	</form>

</div>
