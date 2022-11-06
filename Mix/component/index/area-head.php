<?php if (!empty($this->options->Show_what_1) && in_array('ShowHeadSVG', $this->options->Show_what_1)): ?>
    <section class="paul-intro">
        <!--顶部最大的头像-->
        <div class="intro-avatar "><img src="<?php $this->options->HeaderPhoto(); ?>" style="width:100%" alt="Innei">
        </div>
        <div class="intro-info">
            <h1>
                <!--名字-->
                <div class="texty mask-bottom"><span class=""
                                                     style="opacity: 1; transform: translate(0px, 0%);"><?php $this->options->HeaderName(); ?></span>
                </div>
            </h1>
            <p>
            <span style="opacity: 1; transform: translate(0px, 0%);"
                  class=""><?php $this->options->HeaderMore(); ?></span>
            </p>

            <div class="texty mask-bottom">
                <div class="social-icons" style="opacity: 1; transform: translate(0px, 0px);">
                    <a href="<?php $this->options->HeaderGitHub(); ?>" target="_blank"
                       ks-tag="bottom" ks-text="GitHub" style="color: var(--black);" rel="noreferrer">
                        <svg aria-hidden="true" focusable="false" data-prefix="fab"
                             data-icon="github" class="svg-inline--fa fa-github fa-w-16 " role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512">
                            <path fill="currentColor"
                                  d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z">
                            </path>
                        </svg>
                    </a>
                    <a href="<?php $this->options->HeaderPixiv(); ?>" target="_blank"
                       ks-tag="bottom" ks-text="pixiv" rel="noreferrer">
                        <svg width="16.800003" height="16.799999" viewBox="0 0 16.8 16.8" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <g clip-path="url(#clip2_1)">
                                <path d="M11.5092 4.94077C10.7801 4.18458 9.86459 3.8757 8.83371 3.87261C8.34886 3.87115 7.87238 3.94836 7.4048 4.07695C6.92966 4.20763 6.47501 4.38603 6.05956 4.65619C6.00464 4.69189 5.9826 4.73307 5.98253 4.79883C5.98196 5.28631 5.97707 5.77375 5.97554 6.26123C5.97415 6.70852 5.97525 7.15582 5.97519 7.6031C5.97513 8.53556 5.97606 9.46806 5.9736 10.4005C5.97336 10.4879 5.99936 10.5382 6.08252 10.574C6.37589 10.7004 6.67717 10.8013 6.9875 10.8754C7.49295 10.9962 8.00604 11.0539 8.52502 11.0782C9.07874 11.1042 9.62144 11.0572 10.1465 10.8751C11.1526 10.5262 11.8767 9.87079 12.2702 8.87373C12.5244 8.22979 12.556 7.56028 12.4487 6.88213C12.3314 6.14101 12.0322 5.4832 11.5092 4.94077Z" fill-rule="evenodd" fill="#0097FA"/>
                                <path d="M8.40001 0.286812C3.91924 0.286812 0.286819 3.91922 0.286819 8.4C0.286819 12.8808 3.91924 16.5132 8.40001 16.5132C12.8808 16.5132 16.5132 12.8808 16.5132 8.4C16.5132 3.91922 12.8808 0.286812 8.40001 0.286812ZM13.7669 8.99059C13.4558 9.7712 12.9353 10.3821 12.2459 10.853C11.5284 11.3431 10.7291 11.6168 9.87282 11.7346C8.78418 11.8845 7.72171 11.7385 6.67181 11.4507C6.467 11.3946 6.26549 11.3279 6.06464 11.2591C6.06384 11.8101 5.97578 12.4247 5.97459 12.9757C5.97446 13.0377 5.99614 13.0718 6.05815 13.0906C6.1768 13.1265 6.2921 13.1727 6.39256 13.2481C6.45154 13.2924 6.49413 13.346 6.48811 13.4265C6.48106 13.5209 6.42841 13.5734 6.33337 13.5736C6.00735 13.5743 5.68132 13.5739 5.35529 13.5739C5.03683 13.574 4.7184 13.5722 4.39995 13.5752C4.32225 13.5759 4.2683 13.5541 4.23894 13.4794C4.20944 13.4043 4.22659 13.3407 4.28104 13.285C4.37451 13.1893 4.49728 13.1471 4.6189 13.1045C4.74399 13.0607 4.74686 13.0892 4.74686 12.9263C4.74654 10.4369 4.74654 7.94745 4.74561 5.45798C4.74559 5.41938 4.76575 5.36404 4.72772 5.34594C4.35721 5.6422 3.91605 6.02229 3.64406 6.41458C3.54762 6.54135 3.45679 6.70781 3.52351 6.87605C3.55544 6.95661 3.55151 7.0498 3.52814 7.13803C3.51276 7.19598 3.48817 7.24697 3.43889 7.28347C3.3566 7.34447 3.29994 7.33531 3.24548 7.24926C3.03471 6.91608 2.82565 6.58183 2.61333 6.24966C2.5786 6.19531 2.58052 6.15723 2.62506 6.11034C2.9164 5.80368 3.23303 5.52505 3.56441 5.2632C4.40356 4.60018 5.31842 4.07006 6.32616 3.70622C6.983 3.46907 7.65891 3.31706 8.35576 3.25813C9.16945 3.18932 9.97157 3.24558 10.7571 3.47625C11.5905 3.72102 12.3252 4.1349 12.9221 4.77404C13.5181 5.41217 13.8763 6.1662 13.9912 7.03186C14.0802 7.70232 14.019 8.35813 13.7669 8.99059Z" fill-rule="evenodd" fill="#0097FA"/>
                            </g>
                            <defs>
                                <clipPath id="clip2_1">
                                    <rect width="16.799999" height="16.799999" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </a>
                    <a href="<?php $this->options->HeaderBiliBili(); ?>" target="_blank"
                       ks-tag="bottom" ks-text="哔哩哔哩" rel="noreferrer">
                        <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <path d="M777.514667 131.669333a53.333333 53.333333 0 0 1 0 75.434667L728.746667 255.829333h49.92A160 160 0 0 1 938.666667 415.872v320a160 160 0 0 1-160 160H245.333333A160 160 0 0 1 85.333333 735.872v-320a160 160 0 0 1 160-160h49.749334L246.4 207.146667a53.333333 53.333333 0 1 1 75.392-75.434667l113.152 113.152c3.370667 3.370667 6.186667 7.04 8.448 10.965333h137.088c2.261333-3.925333 5.12-7.68 8.490667-11.008l113.109333-113.152a53.333333 53.333333 0 0 1 75.434667 0z m1.152 231.253334H245.333333a53.333333 53.333333 0 0 0-53.205333 49.365333l-0.128 4.010667v320c0 28.117333 21.76 51.157333 49.365333 53.162666l3.968 0.170667h533.333334a53.333333 53.333333 0 0 0 53.205333-49.365333l0.128-3.968v-320c0-29.44-23.893333-53.333333-53.333333-53.333334z m-426.666667 106.666666c29.44 0 53.333333 23.893333 53.333333 53.333334v53.333333a53.333333 53.333333 0 1 1-106.666666 0v-53.333333c0-29.44 23.893333-53.333333 53.333333-53.333334z m320 0c29.44 0 53.333333 23.893333 53.333333 53.333334v53.333333a53.333333 53.333333 0 1 1-106.666666 0v-53.333333c0-29.44 23.893333-53.333333 53.333333-53.333334z"
                                  fill="#f25d8e">
                            </path>
                        </svg>
                    </a>
                    <!--                    <a href="--><?php //$this->options->HeaderQQ(); ?><!--" target="_blank"-->
                    <!--                       ks-tag="bottom" ks-text="QQ" style="color: rgb(18, 183, 245);" rel="noreferrer">-->
                    <!--                        <svg aria-hidden="true" focusable="false"-->
                    <!--                             data-prefix="fab" data-icon="qq" class="svg-inline--fa fa-qq fa-w-14 " role="img"-->
                    <!--                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">-->
                    <!--                            <path fill="currentColor"-->
                    <!--                                  d="M433.754 420.445c-11.526 1.393-44.86-52.741-44.86-52.741 0 31.345-16.136 72.247-51.051 101.786 16.842 5.192 54.843 19.167 45.803 34.421-7.316 12.343-125.51 7.881-159.632 4.037-34.122 3.844-152.316 8.306-159.632-4.037-9.045-15.25 28.918-29.214 45.783-34.415-34.92-29.539-51.059-70.445-51.059-101.792 0 0-33.334 54.134-44.859 52.741-5.37-.65-12.424-29.644 9.347-99.704 10.261-33.024 21.995-60.478 40.144-105.779C60.683 98.063 108.982.006 224 0c113.737.006 163.156 96.133 160.264 214.963 18.118 45.223 29.912 72.85 40.144 105.778 21.768 70.06 14.716 99.053 9.346 99.704z">-->
                    <!--                            </path>-->
                    <!--                        </svg>-->
                    <!--                    </a>-->
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>