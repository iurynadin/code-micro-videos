import React, { useEffect, useState } from "react";
import MUIDataTable, { MUIDataTableColumn } from "mui-datatables";
import { httpVideo } from "../../util/http";
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender: (value, tableMeta, updateValue) => {
                return value.map(value => value.name).join(', ');
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value,tableMeta, updateValue){
                return <span>{format(parseISO(value), 'dd/mm/yyyy')}</span>;
            }
        }
    }
];

type Props = {};

const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        httpVideo.get('genres').then(
            response => setData(response.data.data)
        )
    }, []);

    return (
        <div>
            <MUIDataTable
                title="Listagem de Genêros"
                columns={columnsDefinition}
                data={data}
            />

        </div>
    );
};

export default Table;
