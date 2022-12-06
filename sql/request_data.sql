select * from dishes where (vegetarian = 'false' and (category = 'Wok' or category = 'Pizza' or category = 'Soup' or category = 'Dessert'));

select * from "order"
inner join basket b on "order".id = b.order_id
where b.user_id = 'ee561650-c21b-41ec-9d31-e3893b86793d' and b.dish_id = '4c13f857-e9a2-41b8-bb17-1b1c36c260e4' and "order".status = 'Delivered';