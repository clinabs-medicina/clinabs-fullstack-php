<?php
function fill_table_row(string $table, array $row) {
    switch($table) {
        case "FARMACIA": {
            $result = $row;

            if(file_exists($_SERVER['DOCUMENT_ROOT']."/data/images/docs/".$row["doc_receita"])) {
                $result["doc_receita"] = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/data/images/docs/".$row["doc_receita"]));
            } else {
                $result["doc_receita"] = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/data/receitas/assinadas/".$row["doc_receita"]));
            }

            break;
        }
        case "PACIENTES": {
            $result = $row;

            $docs = [
                "doc_rg_frente",
                "doc_rg_verso",
                "doc_cpf_frente",
                "doc_cpf_verso",
                "doc_comp_residencia",
                "doc_procuracao",
                "doc_anvisa",
                "doc_termos"
            ];

            foreach($docs as $doc) {
                if(file_exists($_SERVER['DOCUMENT_ROOT']."/data/images/docs/".$row[$doc])) {
                    $result[$doc] = base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/data/images/docs/".$row[$doc]));
                }  else {
                    $result[$doc] = "";
                }
            } 

            break;
        }
        default: {
            $result = $row;
            break;
        }
    }

    return $result;
}