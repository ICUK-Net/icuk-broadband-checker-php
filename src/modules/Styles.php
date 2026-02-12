<?php
$style = $GLOBALS["BroadbandAvailabilitySearchStyleSettings"];
$rstyle = $GLOBALS["BroadbandAvailabilityResultsStyleSettings"];
$astyle = $GLOBALS["BroadbandAvailabilityAddressSelectStyleSettings"];
?>

<style>

@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Serif&family=Open+Sans&display=swap');

#broadband-availability-results {
  font-family: Open Sans, Verdana, HelveticaNeue, Arial, sans-serif;
  border-radius: 4px;
  border-collapse: separate;
  border: 1px solid <?php echo $rstyle->seperators_colour; ?>;
  border-spacing: 0;
  text-align: center;
  <?php
  if ($rstyle->hide_results)
    echo "display: none;";
  ?>
  color: <?php echo $rstyle->text_colour; ?>;
  background-color: <?php echo $rstyle->background_colour; ?>;
}

#broadband-availability-results-head {
  background-color: <?php echo $rstyle->head_background_colour; ?>;
  color: <?php echo $rstyle->head_text_colour; ?>;
}

#broadband-availability-results-head > tr > th {
  padding: 5px;
  font-size: 13px;
  border-left: 1px solid <?php echo $rstyle->seperators_colour; ?>;
}

#broadband-availability-results-body > tr > td:nth-child(1) {
  background-color: <?php echo $rstyle->left_background_colour; ?>;
  color: <?php echo $rstyle->left_text_colour; ?>;
  width: 15%;
  text-align: left;
}

#broadband-availability-results-body > tr > td {
  border-left: 1px solid <?php echo $rstyle->seperators_colour; ?>;
  border-top: 1px solid <?php echo $rstyle->seperators_colour; ?>;
  padding: 6px;
  font-size: 12px;
  width: 15%;
}

.broadband-availability-available,
.broadband-availability-not-available,
.broadband-availability-planned {
  font-weight: 700;
  font-size: 11px;
  padding: 1px 4px 2px;
  border-radius: 3px;
}

.broadband-availability-available {
  background-color: <?php echo $rstyle->available_label_colour; ?>;
  color: <?php echo $rstyle->available_text_colour; ?>;
}

.broadband-availability-not-available {
  background-color: <?php echo $rstyle->not_available_label_colour; ?>;
  color: <?php echo $rstyle->not_available_text_colour; ?>;
}

.broadband-availability-planned {
  background-color: <?php echo $rstyle->planned_label_colour; ?>;
  color: <?php echo $rstyle->planned_text_colour; ?>;
}

.broadband-availability-speed {
  font-size: 11px;
  color: <?php echo $rstyle->text_colour; ?>;
}

.broadband-availability-speed-down {
  font-weight: 600;
}

.broadband-availability-speed-up {
  color: #999;
}

.broadband-availability-date {
  font-size: 10px;
  color: #999;
}

.broadband-availability-dash {
  color: #999;
}

<?php
$hi = $style->button_gradient_high;
$low = $style->button_gradient_low;
?>
.broadband-availability-search-submit {
  background-image: linear-gradient(to bottom, <?php echo $hi; ?>, <?php echo $low; ?>);
  background-repeat: repeat-x;
  font-family: Open Sans, Verdana, HelveticaNeue, Arial, sans-serif;
  padding: 5px 9px;
  font-size: 11px;
  border: 1px solid <?php echo $rstyle->seperators_colour; ?>;
  border-radius: 2px;
  border-color: rgba(0, 0, 0, .1);
  color: <?php echo $style->button_text_colour; ?>;
}

<?php
$hi_hover = $style->button_gradient_high_hover;
$low_hover = $style->button_gradient_low_hover;
?>
.broadband-availability-search-submit:hover {
  background-image: linear-gradient(to bottom, <?php echo $hi_hover; ?>, <?php echo $low_hover; ?>);
  background-repeat: repeat-x;
  cursor: pointer;
}
  
.broadband-availability-search-input {
  font-family: Open Sans, Verdana, HelveticaNeue, Arial, sans-serif;
  border: 1px solid #aaa;
  border-radius: 2px;
  padding: 2px;
  color: <?php echo $style->input_text_colour; ?>;
  background-color: <?php echo $style->input_bg_colour; ?>;
  font-size: 11px;
  line-height: 2;
}

.broadband-availability-search-input:focus {
  border-color: <?php echo $style->input_hover_fade_colour; ?>;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px <?php echo $style->input_hover_fade_colour; ?>;
  outline: 0;
}

.broadband-availability-search-error {
  font-family: Open Sans, Verdana, HelveticaNeue, Arial, sans-serif; 
  color: <?php echo $style->error_message_colour; ?>; 
  font-size: 11px;
}


.broadband-availability-loader {
  border: 2px solid <?php echo $rstyle->loading_circle_secondary_colour; ?>;
  border-radius: 50%;
  border-top: 2px solid <?php echo $rstyle->loading_circle_primary_colour; ?>;
  width: 10px;
  height: 10px;
  animation: broadband-availability-spin 2s linear infinite;
  margin-left: auto;
  margin-right: auto;
}

@keyframes broadband-availability-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

#broadband-availability-address-list {
  font-family: Open Sans, Verdana, HelveticaNeue, Arial, sans-serif;
  width: auto;
  background-color: <?php echo $astyle->background_colour; ?>;
  color: <?php echo $astyle->address_text_colour; ?>;
  border-radius: 4px;
  font-size: 11px;
  border-collapse: separate;
  border: 1px solid <?php echo $astyle->border_colour; ?>;
  border-spacing: 0;
  line-height: 2;
  display: none;
}

#broadband-availability-address-head {
  background-color: <?php echo $astyle->head_background_colour; ?>;
}

#broadband-availability-address-head th {
  border-top-left-radius: 3px;
  padding: 5px;
  border-left: 1px solid <?php echo $astyle->head_border_colour; ?>;
  text-align: center;
}

#broadband-availability-address-head th:first-child {
  border-left: 0 solid <?php echo $astyle->border_colour; ?>;
}

#broadband-availability-address-head input {
  color: <?php echo $astyle->filter_box_text_colour; ?>;
  background-color: <?php echo $astyle->filter_box_background_colour; ?>;
  border: 1px solid <?php echo $astyle->filter_box_border_colour; ?>;
  padding: 4px;
  float: right;
  border-radius: 2px;
}

#broadband-availability-address-head input:focus {
  border-color: <?php echo $astyle->filter_box_hover_fade_colour; ?>;
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px <?php echo $astyle->filter_box_hover_fade_colour; ?>;
  outline: 0;
}

#broadband-availability-address-body td {
  padding: 5px;
  border-top: 1px solid <?php echo $astyle->border_colour; ?>;
}

#broadband-availability-address-body td:nth-child(2) {
  border-left: 1px solid <?php echo $astyle->border_colour; ?>;
}

#broadband-availability-address-body td:first-child {
  text-align: center !important;
}

.broadband-availability-address-nad {
  float: right;
  color: <?php echo $astyle->nad_text_colour; ?>;
}

.broadband-availability-address-uprn {
  float: right;
  color: <?php echo $astyle->nad_text_colour; ?>;
  margin-right: 10px;
  font-size: 10px;
}
</style>