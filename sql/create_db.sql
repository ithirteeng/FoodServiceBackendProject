create type gender as enum ('Male', 'Female');

alter type gender owner to hits;

create type delivery_status as enum ('Delivered', 'InProcess');

alter type delivery_status owner to hits;

create table users
(
    id          uuid default uuid_generate_v4() not null
        primary key,
    fullname    varchar(255)                    not null
        constraint fullname_length_check
            check (length((fullname)::text) >= 1),
    email       varchar(100)                    not null
        unique,
    address     varchar(255),
    birthdate   timestamp,
    phonenumber varchar(25),
    password    varchar(255)                    not null
        constraint password_length_check
            check (length((password)::text) >= 6),
    gender      gender                          not null
);

alter table users
    owner to hits;

create table token_blacklist
(
    value varchar(300) not null
        constraint token_blacklist_pk
            primary key
);

alter table token_blacklist
    owner to hits;

create table dish_category
(
    type varchar(100) not null
        primary key
);

alter table dish_category
    owner to hits;

create table dishes
(
    id          uuid    default uuid_generate_v4() not null
        primary key,
    name        varchar(200)                       not null,
    description text,
    price       numeric                            not null,
    image       text,
    vegetarian  boolean                            not null,
    category    varchar(100)
        references dish_category
            on update cascade on delete cascade,
    rating      numeric default 0.0
);

alter table dishes
    owner to hits;

create table rating
(
    dish_id uuid                not null
        references dishes
            on delete cascade,
    user_id uuid                not null
        references users
            on delete cascade,
    rating  numeric default 0.0 not null,
    constraint rating_pk
        primary key (dish_id, user_id)
);

alter table rating
    owner to hits;

create table "order"
(
    id           uuid default uuid_generate_v4() not null
        primary key,
    deliverytime timestamp                       not null,
    ordertime    timestamp                       not null,
    address      varchar(150)                    not null,
    price        numeric,
    status       delivery_status                 not null,
    user_id      uuid                            not null
        references users
);

alter table "order"
    owner to hits;

create table basket
(
    user_id  uuid              not null
        references users,
    dish_id  uuid              not null
        references dishes,
    order_id uuid
        references "order",
    amount   integer default 1 not null
);

alter table basket
    owner to hits;

create function uuid_nil() returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_nil() owner to gulevskii;

create function uuid_ns_dns() returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_ns_dns() owner to gulevskii;

create function uuid_ns_url() returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_ns_url() owner to gulevskii;

create function uuid_ns_oid() returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_ns_oid() owner to gulevskii;

create function uuid_ns_x500() returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_ns_x500() owner to gulevskii;

create function uuid_generate_v1() returns uuid
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_generate_v1() owner to gulevskii;

create function uuid_generate_v1mc() returns uuid
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_generate_v1mc() owner to gulevskii;

create function uuid_generate_v3(namespace uuid, name text) returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_generate_v3(uuid, text) owner to gulevskii;

create function uuid_generate_v4() returns uuid
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_generate_v4() owner to gulevskii;

create function uuid_generate_v5(namespace uuid, name text) returns uuid
    immutable
    strict
    parallel safe
    language c
as
$$
begin
-- missing source code
end;
$$;

alter function uuid_generate_v5(uuid, text) owner to gulevskii;

create function set_rating() returns trigger
    language plpgsql
as
$$
begin
    update dishes set rating = (select avg(rating.rating) from rating where dish_id = dishes.id);
    return new;
end;

$$;

alter function set_rating() owner to hits;

create trigger update_rating_trigger
    after update
    on rating
    for each row
execute procedure set_rating();

create trigger delete_rating_trigger
    after delete
    on rating
    for each row
execute procedure set_rating();

create trigger insert_rating_trigger
    after insert
    on rating
    for each row
execute procedure set_rating();