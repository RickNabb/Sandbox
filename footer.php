<div id="ftr_container">
	<?php if($back == true){ ?>
	<button id="ftr_back" class="btn btn-info" onclick="lastPage(window.location);">
		<i class="icon-white icon-chevron-left"></i>&nbsp;Back</button>
	<?php } if($next == true){ ?>
	<button id="ftr_next" class="btn btn-info" onclick="nextPage(window.location);">Next&nbsp;
		<i class="icon-white icon-chevron-right"></i></button>
	<?php } if($cancel == true){ ?>
	<a href="#cancelModal" id="ftr_cancel" role="button" class="btn btn-danger" data-toggle="modal">
		<i class="icon-white icon-remove"></i>&nbsp;Cancel</button></a>
	<?php } if($finish == true){ ?>
	<button id="ftr_finish" class="btn btn-success" onclick="nextPage(window.location);">Finish&nbsp;</button>
	<?php } ?>

	<div style="margin-top: 150px">
		<hr />
		<p class="ctrText">Copyright &copy Nick Rabb 2014</p>
		<p class="ctrText">The Sandbox Playground - 1350 Fairport Rd, Fairport, NY 14450</p>
	</div>
</div>