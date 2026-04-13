import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BusinessCertificatesComponent } from './business-certificates.component';

describe('BusinessCertificatesComponent', () => {
  let component: BusinessCertificatesComponent;
  let fixture: ComponentFixture<BusinessCertificatesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BusinessCertificatesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BusinessCertificatesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
