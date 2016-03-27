<table class="tbl_item">
	<tr>
		<td class="tbl_item_td">
			<p class="lst_itemtitle">
				#name# {{if:show_admin_link:<a href="/index.php?module=shop&action=goods_edit&goods=#id#">Редактировать <a href="/index.php?module=shop&action=goods_del&goods=#id#">Удалить</a>}}<br>
				<a class="lst_author" href="/index.php?module=shop&action=view_by_authors&authors=#author#">#author_name#</a><br>
				<a class="lst_author" href="/index.php?module=shop&action=view_by_categories&categories=#category#">#category_name#</a><br>
			</p>
			<table  width="100%" border1="1">
				<tr>
					<td width="80" cellspacing="0"  valign="top" cellpadding="0">
						{{if:image:<img src="#image#" alt="#name#" title="#name#">}}
					</td>
					<td valign="top">

{{if:show_query_form:<form action="/index.php?module=shop&action=requests_add" method="post">Сейчас товара в наличии нет.<br>Вы можете оставить заявку.<br><input type="hidden" name="good" value="#id#">Количество экземпляров: <input type="text" name="qty" size="3"><br><input type="submit" name="do_add" value="Оставить заявку"></form>}}

{{if:show_order_form:<form action="/index.php?module=shop&action=cart_add" method="post"><input type="hidden" name="id" value="#id#"><input type="hidden" name="new_qty" value="0"><table><tr><td class="lst_price"><select style="width:50" name="new_qty">#qty_select#</select>стоимость : #new_price# руб.</td></tr><tr><td><input type="submit" class="button" name="do_add" value="Положить в корзину"></td></tr></table></form>}}


					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="tbl_item_td">
			<table class="tab_descr" border="0">
				<tr>
					<th class="tab_descr_th">
						Дополнительные сведения
					</th>
				</tr>
				<tr>
					<td>
						<table class="props">
							{{if:tags:<tr><th>Метки</th><td>#tags#</td></tr>}}
							{{if:publisher:<tr><th>Издатель</th><td>#publisher#</td></tr>}}
							{{if:year:<tr><th>Год издания</th><td>#year#</td></tr>}}
							{{if:genre:<tr><th>Жанр</th><td>#genre#</td></tr>}}
							{{if:original_name:<tr><th>Оригинальное название</th><td>#original_name#</td></tr>}}
							{{if:format:<tr><th>Формат</th><td>#format#</td></tr>}}
							{{if:language:<tr><th>Язык</th><td>#language#</td></tr>}}
							{{if:pages_qty:<tr><th>Количество страниц</th><td>#pages_qty#</td></tr>}}
							{{if:description:<tr><th>Описание</th><td>#description#</td></tr>}}
							{{if:pdf:<tr><th>PDF</th><td>{{<a href="#pdf#">скачать (#pdf_size# #pdf_measure#)</a>}}</td></tr>}}
							{{if:epub:<tr><th>epub</th><td>{{<a href="#epub#">скачать (#epub_size# #epub_measure#)</a>}}</td></tr>}}
							{{if:mp3:<tr><th>mp3</th><td>{{<a href="#mp3#">скачать (#mp3_size# #mp3_measure#)</a>}}</td></tr>}}
							{{if:images:<tr><td colspan="2"><img src="#images#" alt="#name#" title="#name#"></td></tr>}}
							{{if:embed:<tr><td colspan="2">#embed#</td></tr>}}
							{{if:links:<tr><td colspan="2">#links#</td></tr>}}
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
