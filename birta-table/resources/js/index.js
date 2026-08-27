import DataTable from "./Components/DataTable.vue";

export {
    DataTable,
};

export default {
    install(app) {
        app.component("DataTable", DataTable);
    },
};