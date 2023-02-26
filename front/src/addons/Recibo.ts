import QRCode from 'qrcode'
import { useCounterStore } from 'stores/example-store'
import { DateTime } from 'luxon'
import numbers from 'numeros_to_words'
export class Recibo {
  store = useCounterStore()
  note (sale) {
    return new Promise((resolve, reject) => {
      try {
        QRCode.toDataURL(sale.name + '|' + sale.amount).then(qr => {
          resolve(this.noteFormat(sale, qr))
        }).catch(err => {
          reject(err)
        })
      } catch (e) {
        reject(e)
      }
    })
  }

  style () {
    return `<style>
    .bold{font-weight: bold;}
    .textPrint-h1{font-size: 20px;}
    .textPrint-h5{font-size: 8px;}
    .textPrint-h6{font-size: 7px;}
    .p2{padding: 2px}
    .underline{text-decoration: underline;}
    .center{text-align: center;}
    .right{text-align: right;}
    .border{border: 1px solid black}
    .collapse{border-collapse: collapse;}
    .background{background: #edf2f7}
    .overflow-visible {white-space: initial;}
    .subrrayado{text-decoration: underline;}
</style>`
  }

  kardex (user) {
    if (user.especialidad === undefined) user.especialidad = ''
    return `<html>
<head>
<meta http-equiv='content-type' content='text/html; utf-8'>
${this.style()}
</head>
<body>
<div style="font-size: 11px;font-family: sans-serif;">
<table width="100%"  class="collapse" >
<tr>
    <td colspan="3">
        <div class="p2 bold center textPrint-h1 ">
            SOCIEDAD DE INGENIEROS DE BOLIVIA <br>
            DEPARTAMENTAL ORURO
        </div>
    </td>
</tr>
<tr>
    <td>
    <img src="logoverde.png" alt="">
    </td>
    <td>
    <div class="p2 bold center textPrint-h1 ">
    FICHA PERSONAL
    </div>
    <td>
    <img src="${user.img}" alt="" width="125px">
    </td>
    </td>
</tr>
</table>
<table width="100%"  class="collapse" style="font-size:18px; ">
<tr><td colspan="2" style=" height: 50px;"><b>NOMBRE:</b> <span class="subrrayado"> ${user.paterno} ${user.materno} ${user.nombres}</span></td></tr>
<tr><td style=" height: 50px; " colspan="2"><b>NACIDO EN</b>: <span class="subrrayado">${user.departamento} ${user.nacimiento} </span></td></tr>

<tr><td style=" height: 50px; "><b>FECHA</b>: <span class="subrrayado">${user.fechaNac} </span></td><td style=" height: 50px; "> <b>C.I.</b>: <span class="subrrayado">${user.ci} ${user.expedido}</span></td></tr>
<tr><td style=" height: 50px; " colspan="2"><b>ESPECIALIDAD</b>: <span class="subrrayado">${user.especialidad}</td></tr>
<tr><td style=" height: 50px; " colspan="2"><b>FECHA DE DIPLOMA ACÁDEMICO</b>: <span class="subrrayado">${user.fechaDiploma}</td></tr>
<tr><td style=" height: 50px; "><b>R.N.I. No.</b> <span class="subrrayado">${user.rni} </td><td style=" height: 50px; "><b>FECHA</b>:<span class="subrrayado"> ${user.fechaRegistro}</span></td></tr>

<tr><td style=" height: 50px; " ><b>RECIBO DE INSCRIPCIÓN No</b> <span class="subrrayado">${user.rni} </span></td><td style=" height: 50px; "><b>FECHA</b>: <span class="subrrayado">${user.fechaRegistro}</span></td></tr>

<tr><td style=" height: 50px; " colspan="2"><b>OBSERVACIONES: </b> _________________________________________________________________________<br><br>_________________________________________________________________________<br><br>_________________________________________________________________________</tr>
</table>

</div>
</body>
</html>`
  }

  noteFormat (sale, qr) {
    numbers().Config._setSingular('BOLIVIANO')
    numbers().Config._setPlural('BOLIVIANOS')
    numbers().Config._setCentsSingular('CENTAVO')
    numbers().Config._setCentsPlural('CENTAVOS')

    const literal = numbers(sale.amount).toString()
    return `<html>
<head>
<meta http-equiv='content-type' content='text/html; utf-8'>
${this.style()}
</head>
<body>
<div style="font-size: 11px;font-family: sans-serif;">
<table width="100%"  class="collapse" >
    <tr>
        <td width="33%">
            <div class="p2 bold center">${this.store.cogs.nombre}</div>
            <div class="p2 center">No. Punto 0</div>
        </td>
        <td></td>
        <td width="120px">
            <table>
                <tr>
                    <td valign=top width="130px">
                        <div class="p2 bold">N</div>
                        <div class="p2 bold">Fecha</div>
                    </td>
                    <td>
                        <div class="p2">${sale.id}</div>
                        <div class="p2">${DateTime.now().toFormat('dd/MM/yyyy')}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <div class="p2 center">
                ${this.store.cogs.direccion}
            </div>
            <div class="p2 center">Teléfono: ${this.store.cogs.telefono}</div>
            <div class="p2 center">Oruro</div>
        </td>
        <td></td>
        <td>

        </td>
    </tr>
    <tr>
        <td colspan="3">
            <div class="p2 bold center textPrint-h1 ">
                ${sale.type === 'Ingreso' ? 'RECIBO DE VENTA' : 'RECIBO DE COMPRA'}
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%">
                <tr><td><div class="p2 bold">Nro:</div></td><td><div>${sale.id}</div></td></tr>
                <tr><td><div class="p2 bold">Nombre/Razón Social:</div></td><td><div>${sale.name}</div></td></tr>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table width="100%" class="p2 collapse">
                <tr class="p2 background">
                    <th class="p2 border">CANTIDAD</th>
                    <th class="p2 border">DESCRIPCIÓN</th>
                    <th class="p2 border" width="60px">PRECIO UNITARIO</th>
                    <th class="p2 border">SUBTOTAL</th>
                </tr>
                <tr>
                    <td class="p2 border right">1</td>
                    <td class="p2 border">${sale.description === null ? '' : sale.description}</td>
                    <td class="p2 border right">${sale.amount} Bs</td>
                    <td class="p2 border right">${sale.amount} Bs</td>
                </tr>
                <tr>
                    <td colspan="1"></td>
                    <td class="p2 border right textPrint-h5" colspan="2">TOTAL Bs</td>
                    <td class="p2 border right">${sale.amount} Bs</td>
                </tr>
            </table>
        </td>
    </tr>
        <tr>
        <td colspan="3" >
            <table width="100%">
                <tr>
                    <td class="p2 center" valign=top>
                        <div class="p2 left bold">Son: ${literal}</div>
                        <div class="p2 text-h5">

                        </div>
                    </td>
                    <td>
                        <img src='${qr}' width='89px'>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>
</div>
</body>
</html>
`
  }
}
