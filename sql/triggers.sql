create or replace function set_rating() returns trigger as
$$
begin
    update dishes set rating = (select avg(rating.rating) from rating where dish_id = dishes.id);
    return new;
end;

$$
    language plpgsql;

create or replace trigger update_rating_trigger
    after update
    on rating
    for each row
execute procedure set_rating();

create or replace trigger delete_rating_trigger
    after delete
    on rating
    for each row
execute procedure set_rating();

create or replace trigger insert_rating_trigger
    after insert
    on rating
    for each row
execute procedure set_rating();
