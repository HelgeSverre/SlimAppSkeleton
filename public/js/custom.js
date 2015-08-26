var customerDropdown = $('#customers');

customerDropdown.select2({
    placeholder: "-- Velg Kunde --",
    data: function (params) {
        return {
            q: params.term, // search term
            page: params.page
        };
    },
    ajax: {
        url: "/ajax/customers",
        dataType: 'json',
        delay: 0,
        processResults: function (data, page) {
            // Formats the AJAX data into select2 compatible format
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.site.id
                    }
                })
            };
        },
        cache: true
    }
});

// Redirect to the URL that sets a session for the customer id
customerDropdown.change(function (e) {
    window.location.href = window.location.protocol + "//" + window.location.host + "/select/" + this.value;
});