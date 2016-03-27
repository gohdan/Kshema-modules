<tr>
<input type="hidden" name="entries[]" value="#id#">
<td><select name="floor_#id#">
<option value="1"{{if:selected_floor_1: selected}}>1</option>
<option value="2"{{if:selected_floor_2: selected}}>2</option>
<option value="3"{{if:selected_floor_3: selected}}>3</option>
</select></td>
<td><input type="text" name="number_#id#" value="#number#" size="3"></td>
<td><select name="type_#id#">
<option value="1"{{if:selected_type_1: selected}}>Standard</option>
<option value="2"{{if:selected_type_2: selected}}>Deluxe</option>
<option value="3"{{if:selected_type_3: selected}}>Apartments</option>
</select></td>
</tr>

