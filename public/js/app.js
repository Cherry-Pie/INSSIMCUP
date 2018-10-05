"use strict";

var App =
{
    maxWeek: 0,
    currentWeek: 0,
    canSimulateNextWeek: true,

    init: function()
    {
        this.initSimualtionButtons();
        this.initMatchScorePopups();
    },

    initMatchScorePopups: function()
    {
        $('.home-score').editable({
            success: App.onXEditableSuccess,
            error: App.onXEditableError,
        });
        $('.away-score').editable({
            success: App.onXEditableSuccess,
            error:App.onXEditableError,
        });
    },

    initSimualtionButtons: function()
    {
        $('#next-week-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            App.simulateNextWeek(false);
        });

        $('#play-all-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            App.simulateAllWeeks();
        });
    },

    onXEditableError: function(response, newValue)
    {
        response = response.responseJSON;

        for (let key in response.errors) {
            if (response.errors.hasOwnProperty(key)) {
                response.errors[key].forEach(function(message) {
                    iziToast.error({
                        id: 'error',
                        title: 'Error',
                        message: message,
                        position: 'topRight',
                        transitionIn: 'fadeInDown'
                    });
                });
            }
        }
    },

    onXEditableSuccess: function(response, newValue)
    {
        $('#league-table').replaceWith(response.league_table_html);
        $('#league-predictions').html(response.league_predictions_html);
    },

    simulateAllWeeks: function()
    {
        var startingpoint = $.Deferred();
        startingpoint.resolve();

        var index = App.currentWeek + 1; // cuz we get response for the finished nex week
        while (index <= App.maxWeek){
            startingpoint = startingpoint.pipe(function() {
                return App.simulateNextWeek(true);
            });
            index++;
        }
    },

    simulateNextWeek: function(appendMathResults)
    {
        $('#play-all-btn').addClass('disabled');
        $('#next-week-btn').addClass('disabled');

        return $.ajax({
            type: "GET",
            url: '/simulate-next-week',
            dataType: 'json',
            async: true,
            success: function(response) {
                App.currentWeek = response.week;

                $('#league-table').replaceWith(response.league_table_html);
                $('#league-predictions').html(response.league_predictions_html);

                if (appendMathResults) {
                    $('#match-results').append(response.match_results_html);
                } else {
                    $('#match-results').html(response.match_results_html);
                }

                if (response.is_all_matches_finished) {
                    $('#play-all-btn').hide();
                    $('#next-week-btn').hide();
                }


                $('#play-all-btn').removeClass('disabled');
                $('#next-week-btn').removeClass('disabled');
            }
        }).done(function() {
            App.initMatchScorePopups();
        });
    },

};

$(document).ready(function() {
    App.init();
});
