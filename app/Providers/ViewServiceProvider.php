<?php

namespace App\Providers;

use App\Http\ViewComposers\CurrentCourseViewComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', CurrentCourseViewComposer::class);

        $this->addForminputDirective();
    }

    /**
     * Adds a custom blade directive '@forminput(name, value)' that can automatically generate the name, value and
     * error-message attributes for our Vue form components.
     *
     * For example, writing the following in a blade template:
     * <input-text @forminput('name', $course->name) label="Name" required></input-text>
     * will compile to an equivalent to the following verbose blade template:
     * <input-text name="name" value="{{ old('name') ?? $course->name }}" error-message="{{ $errors->first('name') }}" label="Name" required></input-text>
     *
     * This also works for boolean inputs, where we need to use :value instead of value:
     * <input-checkbox @forminput('agreed', false) label="I agree"></input-checkbox>
     * will generate an equivalent to the following:
     * <input-checkbox name="agreed" :value="{{ (old('agreed') ?? false) ? 'true' : 'false' }}" error-message="{{ $errors->first('agreed') }}" label="I agree"></input-checkbox>
     *
     * The value is optional (except for boolean inputs):
     * <input-text @forminput('name') label="Name"></input-text>
     * will generate an equivalent to the following:
     * <input-text name="name" value="{{ old('name') ?? '' }}" error-message="{{ $errors->first('name') }}" label="Name"></input-text>
     *
     * @return void
     */
    protected function addForminputDirective() {
        Blade::directive('forminput', function ($params) {
            return "<?php [\$forminput_name, \$forminput_value] = [$params, null]; " . <<<'php'
echo ' name="' . e($forminput_name) . '" ';
if (is_bool($forminput_value)) { echo ':value="' . ((old($forminput_name) ?? $forminput_value) ? 'true' : 'false') . '" '; }
else { echo 'value="' . e(old($forminput_name) ?? $forminput_value) . '" '; }
echo 'error-message="' . e($errors->first($forminput_name)) . '" ';
$forminput_name = null; $forminput_value = null; ?>
php;
        });
    }
}
