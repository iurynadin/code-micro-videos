import * as React from 'react';
import { Page } from "../../components/Page";
import { Box, Fab } from "@material-ui/core";
import { Link } from "react-router-dom";
import AddIcon from "@material-ui/icons/Add";
import Table from './Table';


const PageList = () => {
    return (
        <Page title={'Lista de membros do elenco'}>
            <Box dir={'rtl'} paddingBottom={2}>
                <Fab
                    title="Adicionar membro do Elenco"
                    size="small"
                    color={'secondary'}
                    component={Link}
                    to="/cast-members/create"
                >
                    <AddIcon/>
                </Fab>
            </Box>
            <Box>
                <Table/>
            </Box>
        </Page>
    );
};

export default PageList;
