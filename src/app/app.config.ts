import { ApplicationConfig, importProvidersFrom, provideZoneChangeDetection, provideAppInitializer, inject } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { authInterceptor } from './core/interceptors/auth.interceptor';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { provideTranslateHttpLoader } from '@ngx-translate/http-loader';
import { firstValueFrom } from 'rxjs';

import { routes } from './app.routes';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }), 
    provideRouter(routes),
    provideHttpClient(
      withInterceptors([authInterceptor])
    ),
    importProvidersFrom(
      TranslateModule.forRoot({
        loader: provideTranslateHttpLoader({
          prefix: './assets/i18n/',
          suffix: '.json',
        }),
        fallbackLang: 'en',
        lang: 'en'
      })
    ),
    provideAppInitializer(() => {
      const translate = inject(TranslateService);
      const savedLang =
        typeof localStorage !== 'undefined'
          ? localStorage.getItem('app_language')
          : null;
      const lang = savedLang || 'en';

      translate.setFallbackLang('en');
      return firstValueFrom(translate.use(lang));
    }),
  ]
};
