const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const WebSocket = require('ws');
const bcrypt = require('bcrypt');

const app = express();
const port = 3000;
const wss = new WebSocket.Server({ port: 8080 });

const connection = mysql.createConnection({
    host: 'localhost',
    user: 'tu_usuario',
    password: 'tu_contraseña',
    database: 'SistemaTurnos'
});

connection.connect();

app.use(bodyParser.json());

app.post('/guardar_turno', (req, res) => {
    const turno = req.body.codigo;
    connection.query('INSERT INTO turnos (codigo, estado) VALUES (?, "espera")', [turno], (error, results) => {
        if (error) throw error;
        res.send({ status: 'Turno guardado' });
    });
});

app.post('/login', (req, res) => {
    const { usuario, contraseña } = req.body;
    connection.query('SELECT * FROM usuarios WHERE usuario = ?', [usuario], (error, results) => {
        if (error) throw error;
        if (results.length > 0 && bcrypt.compareSync(contraseña, results[0].contraseña)) {
            res.send({ success: true, asesorId: results[0].id });
        } else {
            res.send({ success: false });
        }
    });
});

app.get('/modulos', (req, res) => {
    connection.query('SELECT * FROM modulos', (error, results) => {
        if (error) throw error;
        res.send(results);
    });
});

wss.on('connection', ws => {
    ws.on('message', message => {
        const data = JSON.parse(message);
        if (data.accion === 'solicitarTurno') {
            connection.query('SELECT * FROM turnos WHERE estado = "espera" LIMIT 1', (error, results) => {
                if (error) throw error;
                if (results.length > 0) {
                    const turno = results[0].codigo;
                    ws.send(JSON.stringify({ turno: turno }));
                }
            });
        } else if (data.accion === 'llamar') {
            connection.query('UPDATE turnos SET estado = "llamado", asesor_id = ?, modulo_id = ?, hora_llamado = CURRENT_TIMESTAMP WHERE codigo = ?', [data.asesorId, data.moduloId, data.turno], (error, results) => {
                if (error) throw error;
                connection.query('SELECT * FROM modulos WHERE id = ?', [data.moduloId], (err, modulos) => {
                    if (err) throw err;
                    wss.clients.forEach(client => {
                        if (client.readyState === WebSocket.OPEN) {
                            client.send(JSON.stringify({ turno: data.turno, modulo: modulos[0].nombre }));
                        }
                    });
                });
            });
        }
    });
});

app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
