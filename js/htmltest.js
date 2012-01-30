/*global window, $, jQuery, JSLINT */
/**
 * Testet für Stud.IP, ob die gesehene Seite den Konventionen für 
 * Barrierefreiheit genügt.
 * @author Rasmus Fuhse
 */

STUDIP.htmltest = {
  /**
   * Testet, ob Bilder und Bilder-Buttons korrekt mit title- und alt-Tag 
   * ausgezeichnet sind.
   */
  testImages: function () {
    (function ($) {
      
      //Image-Buttons sollten stets auch ein title-Tag haben (alt ist dann nicht mehr notwendig):
      $.each($("input[type=image]:not([title])"), function (index, element) {
        $(
          $("<div>Der Button <img src=\"" +
            $(element).attr("src") +
            "\" height=\"18px\"> ist nicht barrierefrei ausgezeichnet. " + 
            "Es fehlt ein Title-Attribut. " +
            "Screenreader lesen in dem Fall die ganze URL des Bildes vor.</div>")
        ).dialog({ title: "Button falsch ausgezeichnet!" });
      });
      
      //Testen, ob bei Bildern alt- und title-Tag doppelt (und identisch) auftauchen:  
      $.each($("img[alt][title]"), function (index, element) {
        if ($(element).attr("alt") === $(element).attr("title") && $(element).attr("title") !== "") {
          $(
            $("<div>Das Bild <img src=\"" + 
              $(element).attr("src") +
              "\" height=\"18px\"> ist nicht barrierefrei ausgezeichnet. " + 
              "Es beinhaltet sowohl title-Tag als auch <u>denselben</u> alt-Tag. " +
              "Screenreader lesen in dem Fall den Text doppelt vor.</div>")
          ).dialog({ title: "Bild doppelt ausgezeichnet!" });
        }
      });
      
    }(jQuery));
  },
  
  testLabels: function () {
    (function ($) {
      //Alle nicht gelabelten Formularfelder finden:
      $.each($("input[type=checkbox], input[type=text], input[type=password], input[type=radio], select, textarea"), function (index, element) {
        if ($(element).parents("label").length === 0 
            && (!$(element).attr("id") || $("label[for=" + $(element).attr("id") + "]").length === 0)
            && (!$(element).attr("aria-labelledby") || $("#" + $(element).attr("aria-labelledby")).length === 0)
            && (!$(element).attr("aria-label"))) {
          $(
            $("<div>Das Formularfeld " +
            ($(element).attr("id") ? $(element).attr("id") : $(element).attr("name")) + 
            " hat kein passendes label-Tag oder ARIA-label.</div>")
          ).dialog({ title: "Fehlendes Label für Formularfeld!" });
        }
      });
      //Alle Labels finden, die ins Leere verweisen:
      $.each($("label[for]"), function (index, element) {
        if ($("input#" + $(element).attr("for") + ", select#" + $(element).attr("for") + ", textarea#" + $(element).attr("for")).length === 0) {
          $(
            $("<div>Einem Label ist kein Formularfeld zugeordnet. Das fehlende Formularfeld hieße " +
            $(element).attr("for") + 
            ". Bitte ergänzen Sie diese Information.</div>")
          ).dialog({ title: "Fehlendes Formularfeld für Label!" });
        }
      });
    }(jQuery));
  },
  
  testBlockquotes: function () {
    (function ($) {
      if ($("blockquote:not([class])").length !== 0) {
        $("blockquote:not([class])")
            .css("opacity", 0.8)
            .css("background-image", "url('assets/images/vendor/jquery-ui/ui-bg_diagonals-thick_18_b81900_40x40.png')")
            .css("border", "thin solid yellow");
        $("<div>Es wurde noch ein Blockquote gefunden, das vermutlich " +
          "(es gibt keine CSS-Klasse daran) kein echtes Zitat beinhaltet. " +
          "Blockquotes sollten nie zum Einrücken verwendet werden!</div>").dialog({ title: "Blockquote??" });
      }
    }(jQuery));
  },

  HTMLLint: function () {
    (function ($) {
      var success = JSLINT("<!DOCTYPE html><html>" + $("html").html() + "</html>", {
        on: true
      });
      if (success === false) {
        $.each(JSLINT.errors, function (index, error) {
          if (error && error.evidence) {
            $(
              $("<div>Zeile " + error.line + "<br>" +
              "<i>" + $('<div/>').text(error.evidence).html() + "</i><br>" +
              "<b>" + error.reason + "</b></div>")
            ).dialog({ title: "Fehler in der HTML-Syntax!" });
          }
        });
      }
    }(jQuery));
  }
};

jQuery(function () {
  //AJAX-error-handling:
  jQuery(window.document).ajaxError(function (event, request, ajax_options) {
    //only do something if there is no separate error-handler on the request:
    if (typeof ajax_options.error !== "function") {
      var response; //the response-message to be displayed
      if (request.responseText.indexOf("<!DOCTYPE") !== -1) {
        //only the exception:
        response = jQuery(".messagebox_exception", jQuery(request.responseText));
        //delete x-button: 
        jQuery(".messagebox_buttons", response).remove();
        response = "<div>" + jQuery(response).html() + "</div>";
      } else {
        response = jQuery("<div>" + request.responseText + "</div>");
      }
      //and now output the error within a dialog-window:
      jQuery(response).dialog({
        modal: true,
        show: 'puff',
        hide: 'puff',
        title: "AJAX-Fehler!".toLocaleString(),
        dialogClass: "error"
      });
    }
  });
});

