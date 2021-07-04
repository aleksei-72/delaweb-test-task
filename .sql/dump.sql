--
-- PostgreSQL database dump
--

-- Dumped from database version 13.1 (Ubuntu 13.1-1.pgdg16.04+1)
-- Dumped by pg_dump version 13.1 (Ubuntu 13.1-1.pgdg16.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: pguser
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO pguser;

--
-- Name: organization; Type: TABLE; Schema: public; Owner: pguser
--

CREATE TABLE public.organization (
    id integer NOT NULL,
    title character varying(255) NOT NULL
);


ALTER TABLE public.organization OWNER TO pguser;

--
-- Name: organization_id_seq; Type: SEQUENCE; Schema: public; Owner: pguser
--

CREATE SEQUENCE public.organization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.organization_id_seq OWNER TO pguser;

--
-- Name: token; Type: TABLE; Schema: public; Owner: pguser
--

CREATE TABLE public.token (
    id integer NOT NULL,
    target_user_id integer NOT NULL,
    exp bigint NOT NULL,
    key character varying(255) NOT NULL
);


ALTER TABLE public.token OWNER TO pguser;

--
-- Name: token_id_seq; Type: SEQUENCE; Schema: public; Owner: pguser
--

CREATE SEQUENCE public.token_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.token_id_seq OWNER TO pguser;

--
-- Name: user; Type: TABLE; Schema: public; Owner: pguser
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    invitatory_id integer,
    organization_id integer NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    phone character varying(255) NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE public."user" OWNER TO pguser;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: pguser
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_id_seq OWNER TO pguser;

--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: pguser
--

INSERT INTO public.doctrine_migration_versions VALUES ('DoctrineMigrations\Version20210630125327', '2021-07-04 22:01:08', 446);
INSERT INTO public.doctrine_migration_versions VALUES ('DoctrineMigrations\Version20210701115820', '2021-07-04 22:01:08', 105);


--
-- Data for Name: organization; Type: TABLE DATA; Schema: public; Owner: pguser
--

INSERT INTO public.organization VALUES (1, 'DelaWeb');


--
-- Data for Name: token; Type: TABLE DATA; Schema: public; Owner: pguser
--



--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: pguser
--

INSERT INTO public."user" VALUES (1, 1, 1, 'Антон', 'Кузнецов', '88005553555', '$2y$10$/mmsSswZy.UFB/jPaWjfd.nX1ZIb8ouHzyCPgG11EPg3qoMP5pODe');


--
-- Name: organization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pguser
--

SELECT pg_catalog.setval('public.organization_id_seq', 1, true);


--
-- Name: token_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pguser
--

SELECT pg_catalog.setval('public.token_id_seq', 1, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: pguser
--

SELECT pg_catalog.setval('public.user_id_seq', 1, true);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: organization organization_pkey; Type: CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public.organization
    ADD CONSTRAINT organization_pkey PRIMARY KEY (id);


--
-- Name: token token_pkey; Type: CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public.token
    ADD CONSTRAINT token_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_5f37a13b6c066afe; Type: INDEX; Schema: public; Owner: pguser
--

CREATE INDEX idx_5f37a13b6c066afe ON public.token USING btree (target_user_id);


--
-- Name: idx_8d93d64920baa5f8; Type: INDEX; Schema: public; Owner: pguser
--

CREATE INDEX idx_8d93d64920baa5f8 ON public."user" USING btree (invitatory_id);


--
-- Name: idx_8d93d64932c8a3de; Type: INDEX; Schema: public; Owner: pguser
--

CREATE INDEX idx_8d93d64932c8a3de ON public."user" USING btree (organization_id);


--
-- Name: uniq_5f37a13b8a90aba9; Type: INDEX; Schema: public; Owner: pguser
--

CREATE UNIQUE INDEX uniq_5f37a13b8a90aba9 ON public.token USING btree (key);


--
-- Name: uniq_8d93d649444f97dd; Type: INDEX; Schema: public; Owner: pguser
--

CREATE UNIQUE INDEX uniq_8d93d649444f97dd ON public."user" USING btree (phone);


--
-- Name: uniq_c1ee637c2b36786b; Type: INDEX; Schema: public; Owner: pguser
--

CREATE UNIQUE INDEX uniq_c1ee637c2b36786b ON public.organization USING btree (title);


--
-- Name: token fk_5f37a13b6c066afe; Type: FK CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public.token
    ADD CONSTRAINT fk_5f37a13b6c066afe FOREIGN KEY (target_user_id) REFERENCES public."user"(id);


--
-- Name: user fk_8d93d64920baa5f8; Type: FK CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d64920baa5f8 FOREIGN KEY (invitatory_id) REFERENCES public."user"(id);


--
-- Name: user fk_8d93d64932c8a3de; Type: FK CONSTRAINT; Schema: public; Owner: pguser
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d64932c8a3de FOREIGN KEY (organization_id) REFERENCES public.organization(id);


--
-- PostgreSQL database dump complete
--

