create view avaliacoes_view as(
select av.id, 
av.professor_id, 
av.user_id, 
av.disciplina_id, 
av.turma_id, 
av.aluno_id, 
av.mac1, 
av.p11, 
av.p12,
av.fnj1, 
av.fj1, 
av.mac2, 
av.p21, 
av.p22, 
av.fnj2, 
av.fj2, 
av.mac3, 
av.p31, 
av.p32, 
av.fnj3, 
av.fj3,
year(now())-year(al.data_de_nascimento) as idade,
    ROUND((IFNULL(av.mac1, 0) + 
           IFNULL(av.p11, 0) + 
           IFNULL(av.p12, 0)) / 3,1) AS ct1a,


    ROUND((IFNULL(av.mac1, 0) + 
           IFNULL(av.p11, 0)) / 2,1) AS ct1b,


    ROUND((
        ROUND((IFNULL(av.mac1, 0) + 
               IFNULL(av.p11, 0) + 
               IFNULL(av.p12, 0)) / 3,1) + 

        ROUND((IFNULL(av.mac2, 0) + 
               IFNULL(av.p21, 0) + 
               IFNULL(av.p22, 0)) / 3,1)) / 2,1) AS ct2a,

    ROUND((
        ROUND((IFNULL(av.mac1, 0) + 
               IFNULL(av.p11, 0)) / 2,1) + 

        ROUND((IFNULL(av.mac2, 0) + 
               IFNULL(av.p21, 0)) / 2,1)) / 2,1) AS ct2b,


    ROUND((
      ROUND((
          ROUND((IFNULL(av.mac1, 0) + 
                 IFNULL(av.p11, 0) + 
                 IFNULL(av.p12, 0)) / 3,1) + 

          ROUND((IFNULL(av.mac2, 0) + 
                 IFNULL(av.p21, 0) + 
                 IFNULL(av.p22, 0)) / 3,1)) / 2,1) +

          ROUND((IFNULL(av.mac1, 0) + 
                 IFNULL(av.p11, 0) + 
                 IFNULL(av.p12, 0)) / 3,1))/2,1) AS ct3a,

    ROUND((
      ROUND((
          ROUND((IFNULL(av.mac1, 0) + 
                 IFNULL(av.p11, 0)) / 2,1) + 

          ROUND((IFNULL(av.mac2, 0) + 
                 IFNULL(av.p21, 0)) / 2,1)) / 2,1) +

          ROUND((IFNULL(av.mac1, 0) + 
                 IFNULL(av.p11, 0)) / 2,1))/2,1) AS ct3b,

    ROUND(
      ROUND((
        ROUND((IFNULL(av.mac1, 0) + 
               IFNULL(av.p11, 0) + 
               IFNULL(av.p12, 0)) / 3,1) + 

        ROUND((IFNULL(av.mac2, 0) + 
               IFNULL(av.p21, 0) + 
               IFNULL(av.p22, 0)) / 3,1)) / 2,1)+ 

            ROUND((IFNULL(av.mac3,0) + 
                   IFNULL(av.p31,0)) / 2,1) / 2,1) AS mtca,

    ROUND(
      ROUND((
        ROUND((IFNULL(av.mac1, 0) + 
               IFNULL(av.p11, 0)) / 2,1) + 

        ROUND((IFNULL(av.mac2, 0) + 
               IFNULL(av.p21, 0)) / 2,1)) / 2,1)+ 

            ROUND((IFNULL(av.mac3,0) + 
                   IFNULL(av.p31,0)) / 2,1) / 2,1) AS mtcb,

    ROUND(
        ROUND(
          ROUND((
            ROUND((IFNULL(av.mac1, 0) + 
                   IFNULL(av.p11, 0) + 
                   IFNULL(av.p12, 0)) / 3,1) + 

            ROUND((IFNULL(av.mac2, 0) + 
                   IFNULL(av.p21, 0) + 
                   IFNULL(av.p22, 0)) / 3,1)) / 2,1)+ 

                ROUND((IFNULL(av.mac3,0) + 
                       IFNULL(av.p31,0)) / 2,1) / 2,1) * 0.6,1) AS sessentaa,

    ROUND(
        ROUND(
          ROUND((
            ROUND((IFNULL(av.mac1, 0) + 
                   IFNULL(av.p11, 0)) / 2,1) + 

            ROUND((IFNULL(av.mac2, 0) + 
                   IFNULL(av.p21, 0)) / 2,1)) / 2,1)+ 

                ROUND((IFNULL(av.mac3,0) + 
                       IFNULL(av.p31,0)) / 2,1) / 2,1) * 0.6,1) AS sessentab,
    ROUND(av.p32 * 0.4, 1) AS quarenta,

    ROUND(
        ROUND(
            ROUND(
              ROUND((
                ROUND((IFNULL(av.mac1, 0) + 
                       IFNULL(av.p11, 0) + 
                       IFNULL(av.p12, 0))/ 3,1) + 

                ROUND((IFNULL(av.mac2, 0) + 
                       IFNULL(av.p21, 0)+
                       IFNULL(av.p21, 0)) / 3,1)) / 2,1) + 

                ROUND((IFNULL(av.mac3,0) + 
                       IFNULL(av.p31,0)) / 2,1) / 2,1) * 0.6,1) + 
                       IFNULL(av.p32,0) * 0.4,1) AS notafinala,

    ROUND(
        ROUND(
            ROUND(
              ROUND((
                ROUND((IFNULL(av.mac1, 0) + 
                       IFNULL(av.p11, 0)) / 2,1) + 

                ROUND((IFNULL(av.mac2, 0) + 
                       IFNULL(av.p21, 0)) / 2,1)) / 2,1) + 

                    ROUND((IFNULL(av.mac3,0) + 
                           IFNULL(av.p31,0)) / 2,1) / 2,1) * 0.6,1) + 
                           IFNULL(av.p32,0) * 0.4,1) AS notafinalb,

    round(av.exame2,1) as exame2,

    round(av.exame3,1) as exame3,

    av.status avaliacao_status, 
    av.created_at, 
    av.updated_at,
    atu.numero,
    al.status as aluno_status, 
    al.devedor, 
    al.nome as aluno, 
    al.idmatricula as idmatricula,
    tur.modulo_id,
    tur.nome as turma, 
    tur.ano_lectivo,
    disc.nome as disciplina, 
    disc.acronimo, 
    disc.categoria,
    us.name as usuario
    from avaliacaos av 
    inner join alunos al on av.aluno_id = al.id 
    inner join turmas tur on av.turma_id  = tur.id  
    inner join disciplinas disc on av.disciplina_id = disc.id
    inner join professors prof on av.professor_id = prof.id
    inner join aluno_turma atu on av.aluno_id = atu.aluno_id and tur.id = atu.turma_id
    left  join users us on av.user_id = us.id
    group by av.id);


    create view curso_disciplina as
    select dm.modulo_id, 
    dm.disciplina_id, 
    dm.user_id,
    dm.carga,
    dm.terminal, 
    dm.do_curso, 
    dm.curricular, 
    m.nome as modulo, 
    m.curso_id, 
    m.classe_id,
    m.ano,
    disc.nome as disciplina, 
    disc.acronimo as disc_acr,
    disc.categoria, 
    cur.nome as curso, 
    cur.acronimo curso_acr, 
    cur.professor_id as coordenador_id, 
    cur.area_id, 
    cur.nome_instituto_mae, 
    cur.director_instituto_mae, 
    cl.nome as classe,
    cl.por_extenso,
    prof.nome as coordenador, 
    prof.telefone, 
    prof.email, 
    ar.nome as area, 
    ar.acronimo as area_acr
    from disciplina_modulo dm 
    inner join modulos m  on m.id = dm.modulo_id
    inner join disciplinas disc  on disc.id = dm.disciplina_id
    inner join cursos cur  on cur.id = m.curso_id
    inner join classes cl  on cl.id = m.classe_id
    inner join professors prof on  prof.id = cur.professor_id
    inner join areas ar on ar.id = cur.area_id
    order by m.id;