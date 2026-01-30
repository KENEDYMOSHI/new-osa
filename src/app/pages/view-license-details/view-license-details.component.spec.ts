import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewLicenseDetailsComponent } from './view-license-details.component';

describe('ViewLicenseDetailsComponent', () => {
  let component: ViewLicenseDetailsComponent;
  let fixture: ComponentFixture<ViewLicenseDetailsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ViewLicenseDetailsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ViewLicenseDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
