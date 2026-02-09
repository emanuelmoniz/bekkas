// Local, minimal shim so the editor can resolve `import { defineConfig } from 'cypress'`
// — use only as a temporary/workspace fallback. Install real `cypress` to get full types.

declare module 'cypress' {
  /** minimal shape used by our config file */
  export function defineConfig(config: any): any;
  const _default: any;
  export default _default;
}

declare namespace Cypress {
  interface Chainable<Subject = any> { }
}
