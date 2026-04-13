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

  it('should switch to stickers tab', () => {
    component.setTab('stickers');

    expect(component.activeTab).toBe('stickers');
  });

  it('should open a preview window for a sticker', () => {
    const documentOpen = jasmine.createSpy('open');
    const documentWrite = jasmine.createSpy('write');
    const documentClose = jasmine.createSpy('close');
    const openSpy = spyOn(window, 'open').and.returnValue({
      document: {
        open: documentOpen,
        write: documentWrite,
        close: documentClose
      }
    } as unknown as Window);

    component.previewSticker(component.stickers[0]);

    expect(openSpy).toHaveBeenCalledWith('', '_blank', 'width=900,height=700');
    expect(documentOpen).toHaveBeenCalled();
    expect(documentWrite).toHaveBeenCalled();
    expect(documentClose).toHaveBeenCalled();
    expect(documentWrite.calls.mostRecent().args[0]).toContain(component.stickers[0].stickerNumber);
    expect(documentWrite.calls.mostRecent().args[0]).not.toContain('window.addEventListener("load", function () { window.print(); });');
  });

  it('should include auto print script when printing a sticker', () => {
    const documentWrite = jasmine.createSpy('write');
    spyOn(window, 'open').and.returnValue({
      document: {
        open: jasmine.createSpy('open'),
        write: documentWrite,
        close: jasmine.createSpy('close')
      }
    } as unknown as Window);

    component.printSticker(component.stickers[0]);

    expect(documentWrite.calls.mostRecent().args[0]).toContain('window.addEventListener("load", function () { window.print(); });');
  });
});
