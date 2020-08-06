<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="heading1">
			<h4 class="panel-title">
				<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false" aria-controls="collapse1">Missing Domain Name {if $checker.error.noDomain}<span class="label label-danger">{$checker.error.noDomain|count}</span>{else}<span class="label label-success">Ok</span>{/if}</a>
			</h4>
		</div>
		<div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
			<div class="panel-body">
				<table class="datatable" width="100%">
					<tbody>
						<tr>
							<th>User ID</th>
							<th>Hosting ID</th>
							<th>Server</th>
						</tr>
						{foreach $checker.error.noDomain as $k => $v}
						<tr>
							<td class="text-left">
								<a href="clientssummary.php?userid={$v.userid}">#{$v.userid}</a>
							</td>
							<td class="text-left">
								<a href="clientsservices.php?userid={$v.userid}&id={$v.id}">#{$v.id}</a>
							</td>
							<td class="text-left">
								<a href="configservers.php">{$checker.servers[$v.server].hostname}</a>
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="3" class="text-center">No Issue Found</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="heading2">
			<h4 class="panel-title">
				<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">Missing Plesk Username {if $checker.error.noUsername}<span class="label label-danger">{$checker.error.noUsername|count}</span>{else}<span class="label label-success">Ok</span>{/if}</a>
			</h4>
		</div>
		<div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
			<div class="panel-body">
				<table class="datatable" width="100%">
					<tbody>
						<tr>
							<th>User ID</th>
							<th>Hosting ID</th>
							<th>Domain</th>
							<th>Server</th>
						</tr>
						{foreach $checker.error.noUsername as $k => $v}
						<tr>
							<td class="text-left">
								<a href="clientssummary.php?userid={$v.userid}">#{$v.userid}</a>
							</td>
							<td class="text-left">
								<a href="clientsservices.php?userid={$v.userid}&id={$v.id}">#{$v.id}</a>
							</td>
							<td class="text-left">
								<a href="clientsservices.php?userid={$v.userid}&id={$v.id}">{$v.domain}</a>
							</td>
							<td class="text-left">
								<a href="configservers.php">{$checker.servers[$v.server].hostname}</a>
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="4" class="text-center">No Issue Found</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="heading3">
			<h4 class="panel-title">
				<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">Invalid Plesk Username {if $checker.error.usernameSpace}<span class="label label-danger">{$checker.error.usernameSpace|count}</span>{else}<span class="label label-success">Ok</span>{/if}</a>
			</h4>
		</div>
		<div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
			<style>mark { background-color: #e0637a; }</style>
			<div class="panel-body">
				<table class="datatable" width="100%">
					<tbody>
						<tr>
							<th>User ID</th>
							<th>Hosting ID</th>
							<th>Domain</th>
							<th>Username</th>
							<th>Server</th>
						</tr>
						{foreach $checker.error.usernameSpace as $k => $v}
						<tr>
							<td class="text-left">
								<a href="clientssummary.php?userid={$v.userid}">#{$v.userid}</a>
							</td>
							<td class="text-left">
								<a href="clientsservices.php?userid={$v.userid}&id={$v.id}">#{$v.id}</a>
							</td>
							<td class="text-left">
								<a href="clientsservices.php?userid={$v.userid}&id={$v.id}">{$v.domain}</a>
							</td>
							<td class="text-left">
								{$v.username|replace:' ':'<mark> </mark>'}
							</td>
							<td class="text-left">
								<a href="configservers.php">{$checker.servers[$v.server].hostname}</a>
							</td>
						</tr>
						{foreachelse}
						<tr>
							<td colspan="5" class="text-center">No Issue Found</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
    </div>
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="heading4">
			<h4 class="panel-title">
				<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">External ID not Found in Plesk (Error code: 1013) {if $checker.externalIDCount}<span class="label label-danger">{$checker.externalIDCount}</span>{else}<span class="label label-success">Ok</span>{/if}</a>
			</h4>
		</div>
		<div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
			<div class="panel-body">
				<table class="datatable" width="100%">
					<tbody>
						<tr>
							<th>Server</th>
							<th>User ID</th>
							<th>Hosting</th>
							<th>Domain</th>
							<th>External ID</th>
						</tr>
						{foreach $checker.error.externalID as $k => $v}
						<tr>
							<td class="text-left" style="background-color:#f3f3f3;">
								<a href="configservers.php">{$checker.servers[$k].hostname}</a>
							</td>
							<td colspan="4" style="background-color:#f3f3f3;"></td>
						</tr>
						{foreach $v as $subk => $subv}
						<tr>
							<td></td>
							<td class="text-left">
								<a href="clientssummary.php?userid={$subk}">#{$subk}</a>
							</td>
							<td class="text-left">
								{foreach $subv.accounts as $id => $domain}
								<a href="clientsservices.php?userid={$subk}&id={$id}">#{$id}</a><br>
								{/foreach}
							</td>
							<td class="text-left">
								{foreach $subv.accounts as $id => $domain}
								<a href="clientsservices.php?userid={$subk}&id={$id}">{$domain}</a><br>
								{/foreach}
							</td>
							<td class="text-left">
								<small>{$subv.external_id}</small>
							</td>
						</tr>
						{/foreach}
						{foreachelse}
						<tr>
							<td colspan="5" class="text-center">No Issue Found</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
