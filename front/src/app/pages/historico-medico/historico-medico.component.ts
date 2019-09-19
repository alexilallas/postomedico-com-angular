import { Component, OnInit } from '@angular/core';
import { HistoricoMedico } from './historico-medico';
import { HistoricoMedicoService } from './historico-medico.service';
import { PacienteService } from '../paciente/paciente.service';
import { environment } from '../../../environments/environment';
import { NgxSmartModalService } from 'ngx-smart-modal';
import { DatatablesComponent } from '../../shared/datatables/datatables.component';

@Component({
  selector: 'historico-medico-cmp',
  moduleId: module.id,
  templateUrl: 'historico-medico.component.html'
})

export class HistoricoMedicoComponent extends DatatablesComponent implements OnInit {

  public _fator_rh = ['Positivo', 'Negativo'];
  public dtOptions: DataTables.Settings = {};

  public form = new HistoricoMedico();
  public modal = 'historicoMedicoModal';
  public pacientes: any;
  public isNewHistorico: boolean = false;

  constructor
    (
      private pacienteService: PacienteService,
      public ngxSmartModalService: NgxSmartModalService,
      private historicoMedicoService: HistoricoMedicoService,
  ) {
    super();
    console.log('HistoricoMedicoComponent')
  }

  ngOnInit() {
    this.dtOptions = environment.dtOptions
    this.getPacientes()
  }


  getPacientes(): any {
    this.historicoMedicoService.getPacientes()
      .subscribe(response => {
        console.log('getPacientes',response),
          this.pacientes = response,
          this.rerenderTable()
      })
  }

  save() {
    this.saveHistoricoMedico()
    HistoricoMedicoService.historicoMedicoCreatedAlert.subscribe(
      () => {
        this.eraseForm(),
          this.getPacientes(),
          this.close()
      }
    )
  }

  close() {
    this.eraseForm()
    this.ngxSmartModalService.close(this.modal)
  }

  saveHistoricoMedico() {
    this.historicoMedicoService.postHistorico(this.form)
  }

  openFormEdit(id) {
    this.isNewHistorico = false
    this.form['paciente_id'] = id
    this.historicoMedicoService.getPacienteById(id)
      .subscribe(response => {
        this.form = response
      })
    this.ngxSmartModalService.open(this.modal)
  }

  openFormNew(id) {
    this.isNewHistorico = true
    this.form['paciente_id'] = id
    this.ngxSmartModalService.open(this.modal)
  }

  update() {
    this.updateHistoricoMedico()
    HistoricoMedicoService.historicoMedicoUpdatedAlert.subscribe(
      () => {
        this.eraseForm(),
          this.getPacientes(),
          this.close()
      }
    )
  }

  updateHistoricoMedico() {
    this.historicoMedicoService.updateHistorico(this.form)
  }

  eraseForm() {
    this.form = {}
  }

}