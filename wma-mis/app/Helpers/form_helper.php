<?php
function displayError($validation, $field)
{
  if (isset($validation)) {
    if ($validation->hasError($field)) {

      return str_replace(['The', 'Field'], ['', ''], humanize($validation->getError($field)));
    } else {
      return false;
    }
  }
}


function setSelect(string $value, string $match): string
{
  if ($value ===  $match) {
    return 'selected="selected"';
  } else {
    return '';
  }
}

function formValidation(array $conditions = [], array $optionalFields = []): array
{
  //get all fields from the form 
  $fields = array_keys($_POST);
  //check fields in forms and determine repeated values
  $intersect = array_intersect($fields, $optionalFields);
  //remove optional fields
  $mandatory = array_diff($fields, $intersect);
  //rendering validation rules based on condition and field 

  // return $mandatory;
  // exit;
  $rulesArr = array_map(function ($key) use ($conditions) {
    $condition = '';
    foreach ($conditions as $cond) {
      if ($cond['key'] === $key) {
        $condition = $cond['condition'];
        break;
      }
    }
    return [
      $key => 'required' . $condition,
    ];
  }, $mandatory);

  return array_merge(...$rulesArr);
}

function tokenField()
{
  $tokenName  = csrf_token();
  $hash  = csrf_hash();
  return <<<"HTML"
        <input type="text" hidden  class="token form-control col-6" name="$tokenName" value="$hash">
   HTML;
}
