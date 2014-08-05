<div id="cancelModal" class="modal hide fade" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="cancelModalLabel">Cancel Event</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure you want to cancel your event?</p>
		<p>This action <strong>cannot</strong> be undone!</p>
	</div>
	<div class="modal-footer">
		<input type="button" class="btn btn-info" value="No" data-dismiss="modal" aria-hidden="true" 
			style="width: 80px"/>
		<input type="button" class="btn btn-danger" value="Yes" onclick="cancelEvent();" style="width: 80px"/>
	</div>
</div>