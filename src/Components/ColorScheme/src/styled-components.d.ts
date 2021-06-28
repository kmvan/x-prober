import { colorSchemeProps } from './typings'

declare module 'styled-components' {
  // eslint-disable-next-line @typescript-eslint/no-empty-interface
  export interface DefaultTheme extends colorSchemeProps {}
}
