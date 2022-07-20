@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2 col-md-12">
        <br>
        {{-- NEW --}}
        <div class="card shadow-none">
            <div class="card-header bg-success">
                <span class="card-title"><strong>July 20, 2022 Update 😀!</strong> <i class="text-muted">12:00 AM</i> <br> <i>What's new?</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> Main Menu</h4>
                <ul>
                    <li><strong class="text-primary">Menu Search</strong> is now added, it's right above all the menus on the left menu bar.</li>
                </ul>
                <h4><span class="text-muted"># </span> Billing Module</h4>
                <ul>
                    <li><strong>Application Adjustment</strong> - billing analyst can now mark bills as paid through application adjustment.
                        This feature can be found when browsing the consumer file. A button is added in the bills tab (this button -> <i class="fas fa-check-circle text-info"></i> )
                    Application Adjusment reports can also be printed in the <strong>Reports</strong> menu under Billing.</li>
                    <li><strong>BAPA Renaming</strong> is now possible, which can be accessed in <strong class="text-primary">BAPA -> All BAPA -> Select the BAPA and View</strong></li>
                    <li><strong>BAPA Reading List</strong> printing is also added under BAPA menu.</li>
                </ul>
                <h4><span class="text-muted"># </span> Disconnection Module</h4>
                <ul>
                    <li><strong>Turn Off List</strong> can now also be printed due to the lack of mobile phones in some areas. It can be found Under <strong>Disconnection</strong> menu</li>
                </ul>
                <h4><span class="text-muted"># </span> Tickets Module</h4>
                <ul>
                    <li>MSDs can now update the accomplishment timelines even after execution of tickets.</li>
                </ul>
                <h4><span class="text-muted"># </span>Minor Adjustments and Bugs Fixes</h4>
                <ul>
                    <li>Filtered Meter Reader at generation of NoD</li>
                    <li>Fixed unsorted notice of disconnections</li>
                </ul>
            </div>
        </div>

        {{-- OLD --}}
        {{-- 
        <div class="card shadow-none">
            <div class="card-header bg-success">
                <span class="card-title"><strong>July 11, 2022 Update!</strong> <i class="text-muted">3:42 PM</i> <br> <i>What's new?</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> Account Restrictions</h4>
                <ul>
                    <li>Restrictions for every account has been updated.</li>
                </ul>
                <h4><span class="text-muted"># </span> Major Update Coming This Weekend!</h4>
                <ul>
                    <li><strong class="text-primary">Additional Features</strong> will be added to the system during the weekend. All feature sets shall be posted 
                    here.</li>
                </ul>
                <h4><span class="text-muted"># </span> Bugs & Fixes</h4>
                <ul>
                    <li>Optimized Performance</li>
                    <li>Fixed minor bugs</li>
                </ul>
            </div>
        </div>    
        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title text-muted"><strong>July 9, 2022 Update!</strong> <i class="text-muted">3:11 PM</i> <br> <i>Change Log</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> Billing Analyst Module</h4>
                <ul>
                    <li>Billing analyst can now re-allow the downloading of BAPA Scheds. To do that, go to
                        <span class="text-primary"><strong>BAPA -> BAPA Reading Sched -> (Select Billing Month)</strong></span>. 
                        At the right side of the <strong>Downloaded Status</strong>, you can see a button named <span class="badge badge-warning">Re-allow Download</span>,
                        which clears the "Downloaded" status to allow the BAPAs to redownload their sched.
                    </li>
                </ul>
                <h4><span class="text-muted"># </span> Bugs & Fixes</h4>
                <ul>
                    <li>Fixed errors on Printing of Reading Monitor</li>
                    <li>Fixed bugs on query</li>
                    <li>Cleared database cache for further optimizations</li>
                </ul>
            </div>
        </div>

        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title text-muted"><strong>July 8, 2022 Update!</strong> <i class="text-muted">4:38 AM</i> <br> <i>Change Log</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> CRM Module</h4>
                <ul>
                    <li><strong>Change Name </strong> application is now available and can be accessed at
                        <span class="text-primary"><a href="{{ route('serviceConnections.change-name-search') }}"><strong>Service Connections -> Change Name</strong></a></span></li>
                    <li>Flow of change of name in this software is straight-forward:
                        </strong>
                        <ol>
                            <li><strong>Apply for Change Name</strong> - files a new change name request</li>
                            <li><strong>Pay at Teller</strong> - automatically queues at the teller</li>
                            <li><strong>Assess the Requirements</strong></li>
                            <li><strong>Confirm after Payment</strong> - if both payment and assessment of requirements are done, the user <strong>should confirm</strong>
                                the change name request via <strong class="text-primary">Service Connections -> All Applications -> (<i>Search and View the application name</i>) 
                                -> Look for the Green Check Icon ( <i class="fas fa-check-circle text-success"></i> ) at the bottom of the consumer info page</strong>
                            </li>
                            <li><strong>Automatically Proceeds to Billing Analyst for the Confirmation of Change Name Request</strong> - once confirmed, it will automatically be 
                                queued at the Billing Analyst's <strong>Account Migration Menu</strong>
                            </li>
                        </ol>
                    </li>
                </ul>
                <h4><span class="text-muted"># </span> Billing Analyst Module - Pending Accounts (Account Migration)</h4>
                <ul>
                    <li>All the confirmed <strong>Change Name</strong> applications is queued at <strong><a href="{{ route('serviceAccounts.pending-accounts') }}">
                        Billing Inquiry -> Others -> Pending Accounts</a></strong></li>
                    <li>Just like all normal energized and approved applications, <strong>Change Name</strong> request requires approval and confirmation
                        from the billing analyst in order to successfully change the account name of the consumer.
                    </li>
                    <li>To confirm, just click on the <strong class="text-primary">blue button</strong> at the right side of the queue and
                        it will redirect you to the confirmation page of the change name.
                    </li>
                </ul>
                <h4><span class="text-muted"># </span> Bugs & Fixes</h4>
                <ul>
                    <li>Fixed bugs at Reading Monitor Map</li>
                    <li>Allowed the changing of previous reading at the Manual Readings Menu</li>
                    <li>Re-corrected the flow of Service Connection - Inspection - Payment</li>
                </ul>
            </div>
        </div>

        <div class="card shadow-none">
            <div class="card-header">
                <span class="card-title text-muted"><strong>July 7, 2022 Update!</strong> <i class="text-muted">2:30 PM</i> <br> <i>Change Log</i></span>
            </div>
            <div class="card-body">
                <h4><span class="text-muted"># </span> Billing Analyst Modules</h4>
                <ul>
                    <li>Billing analyst can now print Stuck Ups, Change Meters, No Displays, and Other Unbilled Readings, which can be accessed in 
                        <span class="text-primary"><strong>Meter Reading -> Reading Monitor -> Reading Monitoring Console -> View Report</strong></span></li>
                    <li>Billing analyst can now view and print all <strong>Billed</strong> and <strong>Unbilled Consumers</strong>. A new menu is created under
                        <span class="text-primary"><strong><a href="{{ route('readings.billed-and-unbilled-reports') }}">Reports -> Meter Reading -> Billed & Unbilled</a></strong></span></li>
                    <li><strong>NOTE </strong> that these new report should be in printed in <strong>LANDSCAPE </strong>form.</li>
                </ul>
            </div>
        </div> --}}
    </div>
</div>
@endsection