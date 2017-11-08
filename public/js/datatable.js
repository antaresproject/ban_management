$(document).ready(function () {

    $(document).on('change', '.ban_management-select-status', function () {
        var
                $this = $(this),
                $table = $this.closest('.tbl-c').find('[data-table-init]');

        if ($table.length === 0) {
            return false;
        }

        var
                columnId = $this.data('column'),
                api = $table.dataTable().api(),
                val = $.fn.dataTable.util.escapeRegex($this.val());

        api.column(columnId).search(val, true, false).draw();
    });

});