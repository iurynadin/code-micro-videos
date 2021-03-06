import * as React from 'react';
import { AppBar, Toolbar, Typography, Theme, Button, makeStyles } from "@material-ui/core";
import logo from "../../static/img/logo.png";
import { Menu } from "./Menu";


const useStyles = makeStyles( (theme: Theme) => ({
    toolbar: {
        backgroundColor: '#000'
    },
    title: {
        flexGrow: 1,
        textAlign: "center"
    },
    logo: {
        width: 100,
        [theme.breakpoints.up('sm')]: {
            width: 170
        }
    }
}));

export const Navbar: React.FC = () => {
    const classes = useStyles();

    return (
        <div>
            <AppBar>
                <Toolbar className={classes.toolbar}>
                    <Menu/>
                    <Typography className={classes.title}>
                        <img
                            src={logo}
                            alt="Codeflix"
                            className={classes.logo}
                        />
                    </Typography>
                    <Button color="inherit">Login</Button>
                </Toolbar>
            </AppBar>
        </div>
    );
}
