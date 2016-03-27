<tr>
<input type="hidden" name="entries[]" value="#id#">
<td><input type="text" name="date_from_#id#" value="#date_from#" size="10"></td>
<td><input type="text" name="date_to_#id#" value="#date_to#" size="10"></td>
<td><select name="type_#id#">
<option value="1"{{if:selected_type_1: selected}}>Standard</option>
<option value="2"{{if:selected_type_2: selected}}>Deluxe</option>
<option value="3"{{if:selected_type_3: selected}}>Apartments</option>
</select></td>
<td><input type="text" name="price_#id#" value="#price#" size="3"></td>
</tr>

