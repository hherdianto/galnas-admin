<div class="modal fade" id="visitorInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informasi Visitor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success alert-dismissible" role="alert" id="alert" style="display: none">
                    <strong id="code"></strong> sudah dikonfirmasi
                    <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="alert alert-warning alert-dismissible" role="alert" id="scheduleAlert" style="display: none">
                    Kunjungan hanya berlaku pada tanggal <strong id="alertDate"></strong> pukul <strong id="alertTime"></strong>
                    <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="form-horizontal" role="form" id="form-memberLevel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="email">Email</label>
                                <input disabled id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="nama">Nama</label>
                                <input readonly id="nama" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="event">Event</label>
                                <input disabled id="event" class="form-control">
                                <input disabled id="timeSlot" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="group">Group?</label>
                                <input disabled id="group" class="form-control"> orang
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirm()" id="confirm">
                    Konfirmasi
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="remove()" id="confirm">
                    Hapus
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
