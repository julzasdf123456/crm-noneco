

@foreach ($discoList as $item)
<style>
    @media print {
        @page {
            margin: 10px;
        }
    
        header {
            display: none;
        }
    
        .divider {
            width: 100%;
            margin: 10px auto;
            height: 1px;
            background-color: #dedede;
        }

        .nod-print {
            font-size: 1.2em !important;
            padding-left: 20px;
            padding-right: 20px;
            page-break-after: always;
        }

        .nod-print:last-child {
            page-break-after: auto;
        }
    
    }  
    
    
    .divider {
        width: 100%;
        margin: 10px auto;
        height: 1px;
        background-color: #dedede;
    } 
    </style>
    
    <div class="row nod-print">
        <div class="col-lg-12">
            <br>
            <p>{{ $item->ServiceAccountName }}</p>
            <p>{{ $item->AccountNumber }}</p>
            <p>{{ $item->NetAmount }}</p>
            <br>
            <br>
            <br>
        </div>
    </div>
@endforeach   

<script type="text/javascript">   
    window.print();

    window.setTimeout(function(){
        window.location.href = "{{ route('discoNoticeHistories.generate-nod') }}";
    }, 1000);
</script>
