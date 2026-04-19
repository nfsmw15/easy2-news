  <!-- Page Content -->
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    {BADGE}{UEBERSCHRIFT}
                    <small>von <i class="fa fa-user"></i> {AUTOR}</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="./">Home</a></li>
                    <li><a href="./?p=newsa">News</a></li>
                    <li class="active">{UEBERSCHRIFT}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <hr>
                <p class="text-muted">
                    <i class="fa fa-clock-o"></i> Geschrieben am {DATUM} um {ZEIT} Uhr
                </p>
                <hr>
                <p class="lead">{KURZNEWS}</p>
                <div class="news-content">{NEWS}</div>
                <hr>
                <a href="./?p=newsa" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Zur&uuml;ck zur Übersicht
                </a>
            </div>
        </div>

    </div>
