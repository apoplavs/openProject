									@switch($judge->status)
										@case(1)
										<i class="fa fa-briefcase" aria-hidden="true"></i> на роботі {{ $judge->updated_status }}
											@break
										@case(2)
										<i class="fa fa-medkit" aria-hidden="true"></i> на лікарняному {{ $judge->updated_status }}
										@if($judge->due_date_status)
											- {{ $judge->due_date_status }}
										@endif
											@break
										@case(3)
										<i class="fa fa-calendar-check-o" aria-hidden="true"></i> у відпустці {{ $judge->updated_status }}
										@if($judge->due_date_status)
											- {{ $judge->due_date_status }}
										@endif
											@break
										@case(4)
										<i class="fa fa-calendar-minus-o" aria-hidden="true"></i> відсутній на робочому місці {{ $judge->updated_status }}
										@if($judge->due_date_status)
											- {{ $judge->due_date_status }}
										@endif
											@break
										@case(5)
										<i class="fa fa-calendar-times-o" aria-hidden="true"></i> припиено повноваження {{ $judge->updated_status }}
											@break
									@endswitch